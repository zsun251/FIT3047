<?php
declare(strict_types=1);

namespace App\Controller;

use App\Policy\UserAccess;

/**
 * VitalData Controller
 *
 * @property \App\Model\Table\VitalDataTable $VitalData
 */
class VitalDataController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->viewBuilder()->setLayout('app');
        $this->loadComponent('Flash');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $vitalDataEntity = $this->VitalData->newEmptyEntity();

        if (!$this->Authorization->can($vitalDataEntity, 'index')) {
            $this->Flash->error(__('You do not have permission to access that page.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $filters = [
            'patient_id' => $this->request->getQuery('patient_id'),
            'symptom_filter' => $this->request->getQuery('symptom_filter') ?: 'any',
            'severity' => $this->request->getQuery('severity'),
            'date_preset' => $this->request->getQuery('date_preset') ?: 'any',
            'date_from' => $this->request->getQuery('date_from'),
            'date_to' => $this->request->getQuery('date_to'),
        ];

        $validSymptomFilters = ['any', 'with', 'none'];
        if (!in_array($filters['symptom_filter'], $validSymptomFilters, true)) {
            $filters['symptom_filter'] = 'any';
        }

        $validDatePresets = ['any', 'today', 'last_7_days', 'last_30_days', 'custom'];
        if (!in_array($filters['date_preset'], $validDatePresets, true)) {
            $filters['date_preset'] = 'any';
        }

        $validSeverities = ['mild', 'moderate', 'severe'];
        if (!in_array($filters['severity'], $validSeverities, true)) {
            $filters['severity'] = null;
        }

        if (!empty($filters['severity']) && $filters['symptom_filter'] === 'any') {
            $filters['symptom_filter'] = 'with';
        }

        $query = $this->VitalData->find()
            ->contain(['Patients.Users', 'Symptoms'])
            ->orderByDesc('VitalData.recorded_at');

        $query = $this->Authorization->applyScope($query, 'index');

        if (!empty($filters['patient_id']) && ctype_digit((string)$filters['patient_id'])) {
            $query->where(['VitalData.patient_id' => (int)$filters['patient_id']]);
        }

        if ($filters['symptom_filter'] === 'with') {
            if (!empty($filters['severity'])) {
                $severity = $filters['severity'];

                $query
                    ->matching('Symptoms', function ($q) use ($severity) {
                        return $q->where(['VitalDataSymptoms.severity' => $severity]);
                    })
                    ->distinct(['VitalData.id']);
            } else {
                $query
                    ->matching('Symptoms')
                    ->distinct(['VitalData.id']);
            }
        } elseif ($filters['symptom_filter'] === 'none') {
            $query
                ->notMatching('Symptoms')
                ->distinct(['VitalData.id']);
        }

        $isValidDate = static fn($date): bool =>
            is_string($date) && (bool)preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);

        $applyDateRange = function ($query, \DateTimeImmutable $start, \DateTimeImmutable $end) {
            return $query->where([
                'VitalData.recorded_at >=' => $start->format('Y-m-d H:i:s'),
                'VitalData.recorded_at <' => $end->format('Y-m-d H:i:s'),
            ]);
        };

        $today = new \DateTimeImmutable('today');

        switch ($filters['date_preset']) {
            case 'today':
                $query = $applyDateRange($query, $today, $today->modify('+1 day'));
                break;

            case 'last_7_days':
                $query = $applyDateRange(
                    $query,
                    $today->modify('-6 days'),
                    $today->modify('+1 day')
                );
                break;

            case 'last_30_days':
                $query = $applyDateRange(
                    $query,
                    $today->modify('-29 days'),
                    $today->modify('+1 day')
                );
                break;

            case 'custom':
                $hasFrom = $isValidDate($filters['date_from']);
                $hasTo = $isValidDate($filters['date_to']);

                if ($hasFrom && $hasTo) {
                    $start = new \DateTimeImmutable($filters['date_from'] . ' 00:00:00');
                    $endDate = new \DateTimeImmutable($filters['date_to'] . ' 00:00:00');

                    if ($start > $endDate) {
                        [$start, $endDate] = [$endDate, $start];
                    }

                    $end = $endDate->modify('+1 day');
                    $query = $applyDateRange($query, $start, $end);
                } elseif ($hasFrom) {
                    $start = new \DateTimeImmutable($filters['date_from'] . ' 00:00:00');
                    $query->where([
                        'VitalData.recorded_at >=' => $start->format('Y-m-d H:i:s')
                    ]);
                } elseif ($hasTo) {
                    $end = (new \DateTimeImmutable($filters['date_to'] . ' 00:00:00'))->modify('+1 day');
                    $query->where([
                        'VitalData.recorded_at <' => $end->format('Y-m-d H:i:s')
                    ]);
                }
                break;
        }

        $vitalData = $query->all();

        $patients = $this->VitalData->Patients
            ->find('list', [
                'keyField' => 'id',
                'valueField' => function ($patient) {
                    $name = trim(($patient->user->first_name ?? '') . ' ' . ($patient->user->last_name ?? ''));
                    return $name !== '' ? $name : 'Patient #' . $patient->id;
                },
            ])
            ->contain(['Users'])
            ->orderBy([
                'Users.first_name' => 'ASC',
                'Users.last_name' => 'ASC',
            ])
            ->all();

        $hasFilters = !empty(array_filter([
            $filters['patient_id'],
            $filters['symptom_filter'] !== 'any' ? $filters['symptom_filter'] : null,
            $filters['symptom_filter'] === 'with' ? $filters['severity'] : null,
            $filters['date_preset'] !== 'any' ? $filters['date_preset'] : null,
            $filters['date_preset'] === 'custom' ? $filters['date_from'] : null,
            $filters['date_preset'] === 'custom' ? $filters['date_to'] : null,
        ]));

        $this->set(compact('vitalData', 'patients', 'filters', 'hasFilters'));
    }

    /**
     * View method
     *
     * @param string|null $id Vital Data id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $vitalDataEntity = $this->VitalData->get($id, contain: ['Patients', 'Symptoms']);

        // Check authorization
        if (!$this->Authorization->can($vitalDataEntity, 'view')) {
            $this->Flash->error(__('You do not have permission to view this vital data.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $this->set(compact('vitalDataEntity'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $vitalDataEntity = $this->VitalData->newEmptyEntity();

        if (!$this->Authorization->can($vitalDataEntity, 'add')) {
            $this->Flash->error(__('You do not have permission to add a health record.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $identity = $this->Authentication->getIdentity();

        $patientId = UserAccess::getPatientId($identity);
        $isStaff = UserAccess::isStaff($identity);

        // Get reminder_id from query parameters if present
        $reminderId = $this->request->getQuery('reminder_id');

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Handle patient_id based on user type
            if (!$isStaff) {
                // Patient user - use their own patient ID
                $data['patient_id'] = $patientId;
            } else {
                // Staff user - validate they selected a patient
                if (empty($data['patient_id'])) {
                    $this->Flash->error(__('Please select a patient.'));
                    $patients = $this->VitalData->Patients->find('list', [
                        'keyField' => 'id',
                        'valueField' => 'id',
                        'limit' => 200
                    ])->all();

                    $symptoms = $this->VitalData->Symptoms->find('list', [
                        'keyField' => 'id',
                        'valueField' => 'symptom_name',
                        'limit' => 200
                    ])->toArray();

                    $this->set(compact('vitalDataEntity', 'patients', 'symptoms', 'patientId', 'isStaff'));
                    return;
                }
            }

            $selectedSymptoms = $data['selected_symptoms'] ?? [];
            $symptomSeverities = $data['symptom_severity'] ?? [];

            foreach ($selectedSymptoms as $symptomId => $selected) {
                if ($selected) {
                    $severity = $symptomSeverities[$symptomId] ?? '';

                    if ($severity === '') {
                        $vitalDataEntity->setError("symptom_severity.$symptomId", [
                            'required' => 'Please select a severity for each selected symptom.'
                        ]);
                    }
                }
            }

            $associatedSymptoms = [];

            foreach ($selectedSymptoms as $symptomId => $selected) {
                if ($selected) {
                    $severity = $symptomSeverities[$symptomId] ?? null;

                    if ($severity) {
                        $associatedSymptoms[] = [
                            'id' => $symptomId,
                            '_joinData' => ['severity' => $severity]
                        ];
                    }
                }
            }

            $data['symptoms'] = $associatedSymptoms;

            $vitalDataEntity = $this->VitalData->patchEntity(
                $vitalDataEntity,
                $data,
                ['associated' => ['Symptoms._joinData']]
            );

            if (!$vitalDataEntity->hasErrors() && $this->VitalData->save($vitalDataEntity)) {
                $this->Flash->success(__('The vital data has been saved.'));

                // Complete reminder for patient users
                if (!$isStaff && $patientId) {
                    $remindersTable = $this->fetchTable('Reminders');

                    // Pass reminder_id if it was provided in the URL, otherwise null
                    // If reminder_id is provided: completes that specific reminder
                    // If null: completes the most recent active reminder
                    $remindersTable->completeReminderForVitalData(
                        $patientId,
                        $vitalDataEntity->id,
                        $reminderId ? (int)$reminderId : null
                    );
                }

                if ($isStaff) {
                    return $this->redirect(['action' => 'index']);
                }
                return $this->redirect($this->getRedirectUrl());
            }

            $this->Flash->error(__('The vital data could not be saved. Please, try again.'));
        }

        $patients = [];
        if ($isStaff) {
            $patients = $this->VitalData->Patients
                ->find('list', ['limit' => 200])
                ->contain(['Users'])
                ->all();
        }

        $symptoms = $this->VitalData->Symptoms->find('list', [
            'keyField' => 'id',
            'valueField' => 'symptom_name',
            'limit' => 200
        ])->toArray();

        $this->set(compact('vitalDataEntity', 'patients', 'symptoms', 'patientId', 'isStaff'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Vital Data id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $vitalDataEntity = $this->VitalData->get($id, contain: ['Symptoms']);

        // Check authorization
        if (!$this->Authorization->can($vitalDataEntity, 'edit')) {
            $this->Flash->error(__('You do not have permission to edit this vital data.'));
            return $this->redirect($this->getRedirectUrl());
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            $selectedSymptoms = $data['selected_symptoms'] ?? [];
            $symptomSeverities = $data['symptom_severity'] ?? [];

            foreach ($selectedSymptoms as $symptomId => $selected) {
                if ($selected) {
                    $severity = $symptomSeverities[$symptomId] ?? '';

                    if ($severity === '') {
                        $vitalDataEntity->setError("symptom_severity.$symptomId", [
                            'required' => 'Please select a severity for each selected symptom.'
                        ]);
                    }
                }
            }

            $associatedSymptoms = [];

            foreach ($selectedSymptoms as $symptomId => $selected) {
                if ($selected) {
                    $severity = $symptomSeverities[$symptomId] ?? null;

                    if ($severity) {
                        $associatedSymptoms[] = [
                            'id' => $symptomId,
                            '_joinData' => ['severity' => $severity]
                        ];
                    }
                }
            }

            $data['symptoms'] = $associatedSymptoms;

            $vitalDataEntity = $this->VitalData->patchEntity(
                $vitalDataEntity,
                $data,
                ['associated' => ['Symptoms._joinData']]
            );

            if (!$vitalDataEntity->hasErrors() && $this->VitalData->save($vitalDataEntity)) {
                $this->Flash->success(__('The vital data has been saved.'));
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('The vital data could not be saved. Please, try again.'));
        }

        $patients = $this->VitalData->Patients->find('list', limit: 200)->all();
        $symptoms = $this->VitalData->Symptoms->find('list', limit: 200)->all();
        $this->set(compact('vitalDataEntity', 'patients', 'symptoms'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Vital Data id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $vitalDataEntity = $this->VitalData->get($id);

        // Check authorization
        if (!$this->Authorization->can($vitalDataEntity, 'delete')) {
            $this->Flash->error(__('You do not have permission to delete this vital data.'));
            return $this->redirect($this->getRedirectUrl());
        }

        if ($this->VitalData->delete($vitalDataEntity)) {
            $this->Flash->success(__('The vital data has been deleted.'));
        } else {
            $this->Flash->error(__('The vital data could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Patient history method
     *
     * @param string|null $patientId Patient id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function patientHistory($patientId = null)
    {
        $identity = $this->request->getAttribute('identity');
        $vitalDataEntity = $this->VitalData->newEmptyEntity();

        // Check authorization
        if (!$this->Authorization->can($vitalDataEntity, 'viewHistory')) {
            $this->Flash->error(__('You do not have permission to access this page.'));
            return $this->redirect($this->getRedirectUrl());
        }

        if (UserAccess::isPatient($identity)) {
            $patientId = UserAccess::getPatientId($identity);
        }

        if (!$patientId) {
            $this->Flash->error(__('This page is only accessible to patients with a valid patient record.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $patient = $this->VitalData->Patients->get($patientId);

        $query = $this->VitalData->find()
            ->contain(['Patients'])
            ->orderBy(['VitalData.recorded_at' => 'DESC']);

        // Apply scope
        $query = $this->Authorization->applyScope($query, 'patientHistory');

        $vitalHistory = $this->paginate($query);
        $this->set(compact('patient', 'vitalHistory'));
    }
}
