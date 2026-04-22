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
            ->contain(['Patients']);

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

        if ($this->request->is('post')) {
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
            $user = $this->fetchTable('Users')->get(
                $identity->getIdentifier(),
                contain: ['Patients'],
            );

            $patientId = $user->patient?->id;
        }

        if (!$patientId) {
            $patient = null;
            $vitalHistory = [];
            $this->set(compact('patient', 'vitalHistory'));
            return;
        }

        $patient = $this->VitalData->Patients->find()
            ->where(['Patients.id' => $patientId])
            ->first();

        if (!$patient) {
            $vitalHistory = [];
            $this->set(compact('patient', 'vitalHistory'));
            return;
        }

        $query = $this->VitalData->find()
            ->contain(['Patients'])
            ->where(['VitalData.patient_id' => $patientId])
            ->orderBy(['VitalData.recorded_at' => 'DESC']);

        // Apply scope
        $query = $this->Authorization->applyScope($query, 'patientHistory');

        $vitalHistory = $this->paginate($query);
        $this->set(compact('patient', 'vitalHistory'));
    }
}
