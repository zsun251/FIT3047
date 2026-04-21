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
        // Create an entity to check permissions
        $vitalDataEntity = $this->VitalData->newEmptyEntity();

        // Check if user can access index at all
        if (!$this->Authorization->can($vitalDataEntity, 'index')) {
            $this->Flash->error(__('You do not have permission to access that page.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $query = $this->VitalData->find()
            ->contain(['Patients', 'Symptoms']);
        // Apply scope using the action name
        $query = $this->Authorization->applyScope($query, 'index');
        $vitalData = $this->paginate($query);
        $this->set(compact('vitalData'));
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

        // Check authorization
        if (!$this->Authorization->can($vitalDataEntity, 'add')) {
            $this->Flash->error(__('You do not have permission to add vital data.'));
            return $this->redirect($this->getRedirectUrl());
        }
        $identity = $this->Authentication->getIdentity();

        $patientId = UserAccess::getPatientId($identity);
        $isStaff = UserAccess::isStaff($identity);

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if (!$isStaff) {
                $data['patient_id'] = $patientId;
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
                return $this->redirect(['action' => 'index']);
            }
            //dd($vitalDataEntity->getErrors());
            $this->Flash->error(__('The vital data could not be saved. Please, try again.'));
        }

        $patients = [];
        if ($isStaff) {
            $patients = $this->VitalData->Patients->find('list', [
                'keyField' => 'id',
                'valueField' => 'id',
                'limit' => 200
            ])->all();
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
            $vitalDataEntity = $this->VitalData->patchEntity($vitalDataEntity, $this->request->getData());
            if ($this->VitalData->save($vitalDataEntity)) {
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
}
