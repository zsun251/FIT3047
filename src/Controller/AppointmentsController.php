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
    public function initialize(): void
    {
        parent::initialize();
        $this->viewBuilder()->setLayout('app');
        $this->loadComponent('Flash');
    }

    /**
     * Index method
     *
     * Patients use a dedicated template.
     * Staff continue to use the main index template.
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $identity = $this->request->getAttribute('identity');
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

        $query = $this->Authorization->applyScope($query, 'index');

        if ($identity && UserAccess::isPatient($identity)) {
            $query = $query->orderBy([
                'Appointments.scheduled_at' => 'ASC',
                'Appointments.id' => 'ASC',
            ]);
        } else {
            $query = $query->orderBy([
                'Appointments.scheduled_at' => 'DESC',
                'Appointments.id' => 'DESC',
            ]);
        }

        $appointments = $this->paginate($query);

        $this->set(compact('appointments'));

        if ($identity && UserAccess::isPatient($identity)) {
            return $this->render('upcoming_appointments');
        }
    }

    /**
     * View method
     *
     * @param string|null $id Appointment id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
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
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $appointment = $this->Appointments->newEmptyEntity();

        if (!$this->Authorization->can($appointment, 'add')) {
            $this->Flash->error(__('You do not have permission to add appointments.'));
            return $this->redirect($this->getRedirectUrl());
        }

        if ($this->request->is('post')) {
            $appointment = $this->Appointments->patchEntity($appointment, $this->request->getData());
            if ($this->Appointments->save($appointment)) {
                $this->Flash->success(__('The appointment has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The appointment could not be saved. Please, try again.'));
        }

        $patients = $this->Appointments->Patients->find('list', limit: 200)->all();
        $clinicians = $this->Appointments->Clinicians->find('list', limit: 200)->all();
        $this->set(compact('appointment', 'patients', 'clinicians'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Appointment id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $appointment = $this->Appointments->get($id, contain: []);

        if (!$this->Authorization->can($appointment, 'edit')) {
            $this->Flash->error(__('You do not have permission to edit this appointment.'));
            return $this->redirect($this->getRedirectUrl());
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $appointment = $this->Appointments->patchEntity($appointment, $this->request->getData());
            if ($this->Appointments->save($appointment)) {
                $this->Flash->success(__('The appointment has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The appointment could not be saved. Please, try again.'));
        }

        $patients = $this->Appointments->Patients->find('list', limit: 200)->all();
        $clinicians = $this->Appointments->Clinicians->find('list', limit: 200)->all();
        $this->set(compact('appointment', 'patients', 'clinicians'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Appointment id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
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
}
