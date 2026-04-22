<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Appointments Controller
 *
 * @property \App\Model\Table\AppointmentsTable $Appointments
 */
class AppointmentsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->viewBuilder()->setLayout('app');
        $this->loadComponent('Flash');
    }

    /**
     * Staff appointments index
     *
     * Admins and clinicians use this page.
     * Patients are redirected to their dedicated upcoming appointments page.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        if ($this->getCurrentUserType() === 'patient') {
            return $this->redirect(['action' => 'upcomingAppointments']);
        }

        $appointment = $this->Appointments->newEmptyEntity();

        if (!$this->Authorization->can($appointment, 'index')) {
            $this->Flash->error(__('You do not have permission to access that page.'));
            return $this->redirect($this->getRedirectUrl());
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
     * Patient upcoming appointments page
     *
     * @return \Cake\Http\Response|null|void
     */
    public function upcomingAppointments()
    {
        if ($this->getCurrentUserType() !== 'patient') {
            return $this->redirect(['action' => 'index']);
        }

        $appointment = $this->Appointments->newEmptyEntity();

        if (!$this->Authorization->can($appointment, 'upcomingAppointments')) {
            $this->Flash->error(__('You do not have permission to access that page.'));
            return $this->redirect($this->getRedirectUrl());
        }

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
     * View method
     *
     * @param string|null $id Appointment id.
     * @return \Cake\Http\Response|null|void
     */
    public function view($id = null)
    {
        $appointment = $this->Appointments->get($id, contain: [
            'Patients.Users',
            'Clinicians.Users',
        ]);

        if (!$this->Authorization->can($appointment, 'view')) {
            $this->Flash->error(__('You do not have permission to view this appointment.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $this->set(compact('appointment'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $appointment = $this->Appointments->newEmptyEntity();

        if (!$this->Authorization->can($appointment, 'add')) {
            $this->Flash->error(__('You do not have permission to add appointments.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $this->ensureAppointmentParticipantRecords();

        if ($this->request->is('post')) {
            $data = $this->combineScheduledDateTime($this->request->getData());

            if (empty($data['status'])) {
                $data['status'] = 'scheduled';
            }

            $appointment = $this->Appointments->patchEntity($appointment, $data);

            if ($this->Appointments->save($appointment)) {
                $this->Flash->success(__('The appointment has been saved.'));
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('The appointment could not be saved. Please, try again.'));
        }

        $patients = $this->buildPatientOptions();
        $clinicians = $this->buildClinicianOptions();

        $this->set(compact('appointment', 'patients', 'clinicians'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Appointment id.
     * @return \Cake\Http\Response|null|void
     */
    public function edit($id = null)
    {
        $appointment = $this->Appointments->get($id, contain: []);

        if (!$this->Authorization->can($appointment, 'edit')) {
            $this->Flash->error(__('You do not have permission to edit this appointment.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $this->ensureAppointmentParticipantRecords();

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->combineScheduledDateTime($this->request->getData());
            $appointment = $this->Appointments->patchEntity($appointment, $data);

            if ($this->Appointments->save($appointment)) {
                $this->Flash->success(__('The appointment has been saved.'));
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('The appointment could not be saved. Please, try again.'));
        }

        $patients = $this->buildPatientOptions();
        $clinicians = $this->buildClinicianOptions();

        $this->set(compact('appointment', 'patients', 'clinicians'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Appointment id.
     * @return \Cake\Http\Response|null
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $appointment = $this->Appointments->get($id);

        if (!$this->Authorization->can($appointment, 'delete')) {
            $this->Flash->error(__('You do not have permission to delete this appointment.'));
            return $this->redirect($this->getRedirectUrl());
        }

        if ($this->Appointments->delete($appointment)) {
            $this->Flash->success(__('The appointment has been deleted.'));
        } else {
            $this->Flash->error(__('The appointment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Get the current user's type directly from the database.
     *
     * @return string|null
     */
    private function getCurrentUserType(): ?string
    {
        $identity = $this->request->getAttribute('identity');

        if (!$identity) {
            return null;
        }

        $userId = (int)$identity->getIdentifier();

        if ($userId <= 0) {
            return null;
        }

        $user = $this->fetchTable('Users')->find()
            ->select(['id', 'type'])
            ->where(['Users.id' => $userId])
            ->first();

        if ($user && is_string($user->type)) {
            return strtolower(trim($user->type));
        }

        return null;
    }

    /**
     * Ensure patient and clinician role records exist for typed users.
     *
     * @return void
     */
    private function ensureAppointmentParticipantRecords(): void
    {
        $usersTable = $this->fetchTable('Users');
        $patientsTable = $this->fetchTable('Patients');
        $cliniciansTable = $this->fetchTable('Clinicians');

        $patientUsers = $usersTable->find()
            ->select(['id'])
            ->where(['Users.type' => 'patient'])
            ->all();

        foreach ($patientUsers as $user) {
            $patientsTable->findOrCreate([
                'user_id' => $user->id,
            ]);
        }

        $clinicianUsers = $usersTable->find()
            ->select(['id'])
            ->where(['Users.type' => 'clinician'])
            ->all();

        foreach ($clinicianUsers as $user) {
            $cliniciansTable->findOrCreate([
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Build patient dropdown options keyed by patient record id.
     *
     * @return array<int, string>
     */
    private function buildPatientOptions(): array
    {
        $patients = $this->fetchTable('Patients')->find()
            ->contain(['Users'])
            ->orderBy([
                'Users.first_name' => 'ASC',
                'Users.last_name' => 'ASC',
                'Users.username' => 'ASC',
            ])
            ->all();

        $options = [];

        foreach ($patients as $patient) {
            $firstName = trim((string)($patient->user->first_name ?? ''));
            $lastName = trim((string)($patient->user->last_name ?? ''));
            $username = trim((string)($patient->user->username ?? ''));

            $fullName = trim($firstName . ' ' . $lastName);

            if ($fullName !== '' && $username !== '') {
                $label = $fullName . ' (@' . $username . ')';
            } elseif ($fullName !== '') {
                $label = $fullName;
            } elseif ($username !== '') {
                $label = '@' . $username;
            } else {
                $label = 'Patient #' . $patient->id;
            }

            $options[(int)$patient->id] = $label;
        }

        return $options;
    }

    /**
     * Build clinician dropdown options keyed by clinician record id.
     *
     * @return array<int, string>
     */
    private function buildClinicianOptions(): array
    {
        $clinicians = $this->fetchTable('Clinicians')->find()
            ->contain(['Users'])
            ->orderBy([
                'Users.first_name' => 'ASC',
                'Users.last_name' => 'ASC',
                'Users.username' => 'ASC',
            ])
            ->all();

        $options = [];

        foreach ($clinicians as $clinician) {
            $firstName = trim((string)($clinician->user->first_name ?? ''));
            $lastName = trim((string)($clinician->user->last_name ?? ''));
            $username = trim((string)($clinician->user->username ?? ''));

            $fullName = trim($firstName . ' ' . $lastName);

            if ($fullName !== '' && $username !== '') {
                $label = $fullName . ' (@' . $username . ')';
            } elseif ($fullName !== '') {
                $label = $fullName;
            } elseif ($username !== '') {
                $label = '@' . $username;
            } else {
                $label = 'Clinician #' . $clinician->id;
            }

            $options[(int)$clinician->id] = $label;
        }

        return $options;
    }

    /**
     * Combine separate date/time form fields into scheduled_at.
     *
     * @param array<string, mixed> $data Request data
     * @return array<string, mixed>
     */
    private function combineScheduledDateTime(array $data): array
    {
        $scheduledDate = trim((string)($data['scheduled_date'] ?? ''));

        $scheduledHour = trim((string)($data['scheduled_hour'] ?? ''));
        $scheduledMinute = trim((string)($data['scheduled_minute'] ?? ''));
        $scheduledMeridiem = strtoupper(trim((string)($data['scheduled_meridiem'] ?? '')));

        if (
            $scheduledDate !== '' &&
            $scheduledHour !== '' &&
            $scheduledMinute !== '' &&
            in_array($scheduledMeridiem, ['AM', 'PM'], true)
        ) {
            $hour = (int)$scheduledHour;
            $minute = (int)$scheduledMinute;

            if ($hour < 1 || $hour > 12) {
                $hour = 12;
            }

            if (!in_array($minute, [0, 15, 30, 45], true)) {
                $minute = 0;
            }

            if ($scheduledMeridiem === 'AM') {
                $hour24 = $hour === 12 ? 0 : $hour;
            } else {
                $hour24 = $hour === 12 ? 12 : $hour + 12;
            }

            $data['scheduled_at'] = sprintf(
                '%s %02d:%02d:00',
                $scheduledDate,
                $hour24,
                $minute
            );
        }

        unset(
            $data['scheduled_date'],
            $data['scheduled_hour'],
            $data['scheduled_minute'],
            $data['scheduled_meridiem']
        );

        return $data;
    }
}
