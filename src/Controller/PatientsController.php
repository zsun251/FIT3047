<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Patients Controller
 *
 * @property \App\Model\Table\PatientsTable $Patients
 */
class PatientsController extends AppController
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
        $patient = $this->Patients->newEmptyEntity();

        // Authorize before any operations
        $this->Authorization->authorize($patient, 'index');

        $query = $this->Patients->find()
            ->contain(['Users']);

        $query = $this->Authorization->applyScope($query, 'index')
            ->orderBy([
                'Patients.id' => 'DESC',
            ]);

        $patients = $this->paginate($query);

        $this->set(compact('patients'));
    }

    /**
     * View method
     *
     * @param string|null $id Patient id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // TODO Removed VitalsReminders as the Table does not yet exist
        //$patient = $this->Patients->get($id, contain: ['Users', 'Clinicians', 'Appointments', 'VitalData', 'VitalsReminders']);
        $patient = $this->Patients->get($id, contain: ['Users', 'Clinicians', 'Appointments', 'VitalData']);

        // Check authorization
        if (!$this->Authorization->can($patient, 'view')) {
            $this->Flash->error(__('You do not have permission to view this patient.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $this->set(compact('patient'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $patient = $this->Patients->newEmptyEntity();

        // Check authorization
        if (!$this->Authorization->can($patient, 'add')) {
            $this->Flash->error(__('You do not have permission to add patients.'));
            return $this->redirect($this->getRedirectUrl());
        }

        if ($this->request->is('post')) {
            $patient = $this->Patients->patchEntity($patient, $this->request->getData());
            if ($this->Patients->save($patient)) {
                $this->Flash->success(__('The patient has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The patient could not be saved. Please, try again.'));
        }
        $users = $this->Patients->Users->find('list', limit: 200)->all();
        $clinicians = $this->Patients->Clinicians->find('list', limit: 200)->all();
        $this->set(compact('patient', 'users', 'clinicians'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Patient id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $patient = $this->Patients->get($id, contain: ['Clinicians', 'Users']);

        // Check authorization
        if (!$this->Authorization->can($patient, 'edit')) {
            $this->Flash->error(__('You do not have permission to edit this patient.'));
            return $this->redirect($this->getRedirectUrl());
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $patient = $this->Patients->patchEntity($patient, $this->request->getData());
            if ($this->Patients->save($patient)) {
                $this->Flash->success(__('The patient has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The patient could not be saved. Please, try again.'));
        }
        $users = $this->Patients->Users->find('list', limit: 200)->all();
        $clinicians = $this->Patients->Clinicians
            ->find('list', ['limit' => 200])
            ->contain(['Users'])
            ->all();
        $this->set(compact('patient', 'users', 'clinicians'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Patient id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $patient = $this->Patients->get($id);

        // Check authorization
        if (!$this->Authorization->can($patient, 'delete')) {
            $this->Flash->error(__('You do not have permission to delete this patient.'));
            return $this->redirect(['action' => 'index']);
        }

        if ($this->Patients->delete($patient)) {
            $this->Flash->success(__('The patient has been deleted.'));
        } else {
            $this->Flash->error(__('The patient could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    // bella 4.2, 4.3
    public function patientHistory($patientId = null)
    {
        // get logged-in clinician
        $clinicianId = $this->request->getAttribute('identity')->get('id');

        // check patient belongs to this clinician (security gate)
        $patient = $this->Clinicians->Patients->find()
            ->matching('Clinicians', function ($q) use ($clinicianId) {
                return $q->where(['Clinicians.id' => $clinicianId]);
            })
            ->where(['Patients.id' => $patientId])
            ->first();

        if (!$patient) {
            throw new \Cake\Http\Exception\ForbiddenException('Not allowed');
        }

        // fetch vital history
        $vitalHistory = $this->Clinicians->Patients->VitalData->find()
            ->where(['patient_id' => $patientId])
            ->contain(['Symptoms'])
            ->orderBy(['recorded_at' => 'ASC']);

        $this->set(compact('patient', 'vitalHistory'));
    }
}
