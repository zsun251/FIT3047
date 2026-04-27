<?php
declare(strict_types=1);

namespace App\Controller;

use App\Policy\UserAccess;

/**
 * Appointments Controller
 *
 * @property \App\Model\Table\AppointmentsTable $Appointments
 */
class AppointmentsController extends AppController
{
    /**
     * Appointment status values allowed by the existing database constraint.
     */
    private const APPOINTMENT_STATUS_OPTIONS = [
        'upcoming' => 'Upcoming',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public function initialize(): void
    {
        parent::initialize();
        $this->viewBuilder()->setLayout('app');
        $this->loadComponent('Flash');
    }

    /**
     * Staff appointments index.
     *
     * Admins and clinicians use this page. Patients are redirected to the
     * dedicated upcoming appointments page.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $identity = $this->request->getAttribute('identity');
        $appointment = $this->Appointments->newEmptyEntity();

        // Authorize before redirecting. Authorization middleware requires
        // an authorization check even when returning a redirect response.
        $this->Authorization->authorize($appointment, 'index');

        if ($identity && UserAccess::isPatient($identity)) {
            return $this->redirect(['action' => 'upcomingAppointments']);
        }

        $query = $this->Appointments->find()
            ->contain([
                'Patients.Users',
                'Clinicians.Users',
            ]);

        $query = $this->Authorization->applyScope($query, 'index')
            ->orderBy([
                'Appointments.scheduled_at' => 'DESC',
                'Appointments.id' => 'DESC',
            ]);

        $appointments = $this->paginate($query);
        $this->set(compact('appointments'));
    }

    /**
     * Patient upcoming appointments page.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function upcomingAppointments()
    {
        $appointment = $this->Appointments->newEmptyEntity();

        $this->Authorization->authorize($appointment, 'upcomingAppointments');

        $query = $this->Appointments->find()
            ->contain([
                'Patients.Users',
                'Clinicians.Users',
            ]);

        $query = $this->Authorization->applyScope($query, 'upcomingAppointments')
            ->orderBy([
                'Appointments.scheduled_at' => 'ASC',
                'Appointments.id' => 'ASC',
            ]);

        $appointments = $this->paginate($query);
        $this->set(compact('appointments'));

        return $this->render('upcoming_appointments');
    }

    /**
     * View method.
     *
     * @param string|null $id Appointment id.
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $appointment = $this->Appointments->get($id, contain: [
            'Patients.Users',
            'Clinicians.Users',
        ]);

        $this->Authorization->authorize($appointment, 'view');

        $this->set(compact('appointment'));
    }

    /**
     * Add method.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $appointment = $this->Appointments->newEmptyEntity();

        $this->Authorization->authorize($appointment, 'add');

        if ($this->request->is('post')) {
            $data = $this->combineScheduledDateTime($this->request->getData());

            // The existing database only allows upcoming, completed, cancelled.
            // New appointments should always start as upcoming.
            $data['status'] = 'upcoming';

            $appointment = $this->Appointments->patchEntity($appointment, $data);

            // Re-authorize after patching so clinicians cannot submit
            // manipulated patient_id or clinician_id values.
            $this->Authorization->authorize($appointment, 'add');

            if ($this->Appointments->save($appointment)) {
                $this->Flash->success(__('The appointment has been saved.'));
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('The appointment could not be saved. Please, try again.'));
        }

        ['patients' => $patients, 'clinicians' => $clinicians] = $this->getParticipantOptions();
        $this->set(compact('appointment', 'patients', 'clinicians'));
    }

    /**
     * Edit method.
     *
     * @param string|null $id Appointment id.
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $appointment = $this->Appointments->get($id, contain: []);

        $this->Authorization->authorize($appointment, 'edit');

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->combineScheduledDateTime($this->request->getData());

            if (array_key_exists('status', $data)) {
                $status = $this->normaliseAppointmentStatus($data['status']);

                if ($status === null) {
                    $appointment->setError('status', [
                        'valid' => __('Please select a valid appointment status.'),
                    ]);
                    $this->Flash->error(__('The appointment could not be saved. Please, try again.'));

                    ['patients' => $patients, 'clinicians' => $clinicians] = $this->getParticipantOptions();
                    $appointmentStatusOptions = self::APPOINTMENT_STATUS_OPTIONS;
                    $this->set(compact('appointment', 'patients', 'clinicians', 'appointmentStatusOptions'));
                    return;
                }

                $data['status'] = $status;
            }

            $appointment = $this->Appointments->patchEntity($appointment, $data);

            // Re-authorize after patching so clinicians cannot submit
            // manipulated patient_id or clinician_id values.
            $this->Authorization->authorize($appointment, 'edit');

            if ($this->Appointments->save($appointment)) {
                $this->Flash->success(__('The appointment has been saved.'));
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('The appointment could not be saved. Please, try again.'));
        }

        ['patients' => $patients, 'clinicians' => $clinicians] = $this->getParticipantOptions();
        $appointmentStatusOptions = self::APPOINTMENT_STATUS_OPTIONS;
        $this->set(compact('appointment', 'patients', 'clinicians', 'appointmentStatusOptions'));
    }

    /**
     * Delete method.
     *
     * @param string|null $id Appointment id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $appointment = $this->Appointments->get($id);

        $this->Authorization->authorize($appointment, 'delete');

        if ($this->Appointments->delete($appointment)) {
            $this->Flash->success(__('The appointment has been deleted.'));
        } else {
            $this->Flash->error(__('The appointment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Build patient and clinician dropdown options.
     *
     * @return array{patients: array<int, string>, clinicians: array<int, string>}
     */
    private function getParticipantOptions(): array
    {
        $patients = $this->Appointments->Patients->find()
            ->contain(['Users'])
            ->orderBy([
                'Users.first_name' => 'ASC',
                'Users.last_name' => 'ASC',
                'Users.username' => 'ASC',
            ])
            ->limit(200)
            ->all()
            ->combine('id', fn($patient): string => $this->formatParticipantLabel($patient, 'Patient'))
            ->toArray();

        $clinicians = $this->Appointments->Clinicians->find()
            ->contain(['Users'])
            ->orderBy([
                'Users.first_name' => 'ASC',
                'Users.last_name' => 'ASC',
                'Users.username' => 'ASC',
            ])
            ->limit(200)
            ->all()
            ->combine('id', fn($clinician): string => $this->formatParticipantLabel($clinician, 'Clinician'))
            ->toArray();

        return compact('patients', 'clinicians');
    }

    /**
     * Format a patient or clinician option label from the related user record.
     *
     * @param object $participant Patient or clinician entity
     * @param string $fallbackPrefix Label prefix if user details are missing
     * @return string
     */
    private function formatParticipantLabel(object $participant, string $fallbackPrefix): string
    {
        $firstName = trim((string)($participant->user->first_name ?? ''));
        $lastName = trim((string)($participant->user->last_name ?? ''));
        $username = trim((string)($participant->user->username ?? ''));
        $fullName = trim($firstName . ' ' . $lastName);

        if ($fullName !== '' && $username !== '') {
            return $fullName . ' (@' . $username . ')';
        }

        if ($fullName !== '') {
            return $fullName;
        }

        if ($username !== '') {
            return '@' . $username;
        }

        return $fallbackPrefix . ' #' . (int)$participant->id;
    }

    /**
     * Combine separate date/time form fields into scheduled_at.
     *
     * The edit form still supports the default scheduled_at input. Split fields
     * are only processed when the appointment form submits them.
     *
     * @param array<string, mixed> $data Request data
     * @return array<string, mixed>
     */
    private function combineScheduledDateTime(array $data): array
    {
        $hasSplitScheduleFields = array_key_exists('scheduled_date', $data)
            || array_key_exists('scheduled_hour', $data)
            || array_key_exists('scheduled_minute', $data)
            || array_key_exists('scheduled_meridiem', $data);

        if (!$hasSplitScheduleFields) {
            return $data;
        }

        $scheduledDate = trim((string)($data['scheduled_date'] ?? ''));
        $scheduledHour = trim((string)($data['scheduled_hour'] ?? ''));
        $scheduledMinute = trim((string)($data['scheduled_minute'] ?? ''));
        $scheduledMeridiem = strtoupper(trim((string)($data['scheduled_meridiem'] ?? '')));

        $hour = filter_var($scheduledHour, FILTER_VALIDATE_INT);
        $validDate = preg_match('/^\d{4}-\d{2}-\d{2}$/', $scheduledDate) === 1;
        $validHour = is_int($hour) && $hour >= 1 && $hour <= 12;
        $validMinute = in_array($scheduledMinute, ['00', '15', '30', '45'], true);
        $validMeridiem = in_array($scheduledMeridiem, ['AM', 'PM'], true);

        if ($validDate && $validHour && $validMinute && $validMeridiem) {
            if ($scheduledMeridiem === 'AM') {
                $hour24 = $hour === 12 ? 0 : $hour;
            } else {
                $hour24 = $hour === 12 ? 12 : $hour + 12;
            }

            $data['scheduled_at'] = sprintf(
                '%s %02d:%s:00',
                $scheduledDate,
                $hour24,
                $scheduledMinute
            );
        } else {
            $data['scheduled_at'] = null;
        }

        unset(
            $data['scheduled_date'],
            $data['scheduled_hour'],
            $data['scheduled_minute'],
            $data['scheduled_meridiem']
        );

        return $data;
    }

    /**
     * Normalise appointment statuses to values allowed by the existing database.
     *
     * @param mixed $status Submitted status
     * @return string|null
     */
    private function normaliseAppointmentStatus(mixed $status): ?string
    {
        $value = strtolower(trim((string)$status));

        return match ($value) {
            'upcoming', 'scheduled', 'confirmed' => 'upcoming',
            'completed' => 'completed',
            'cancelled', 'canceled' => 'cancelled',
            default => null,
        };
    }
}
