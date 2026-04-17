<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Clinicians Controller
 *
 * @property \App\Model\Table\CliniciansTable $Clinicians
 */
class CliniciansController extends AppController
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
        $clinician = $this->Clinicians->newEmptyEntity();
        $this->Authorization->authorize($clinician);

        $query = $this->Clinicians->find()
            ->contain(['Users']);
        $clinicians = $this->paginate($query);

        $this->set(compact('clinicians'));
    }

    /**
     * View method
     *
     * @param string|null $id Clinician id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $clinician = $this->Clinicians->get($id, contain: ['Users', 'Patients', 'Appointments', 'Resources']);
        $this->Authorization->authorize($clinician);
        $this->set(compact('clinician'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $clinician = $this->Clinicians->newEmptyEntity();
        $this->Authorization->authorize($clinician);
        if ($this->request->is('post')) {
            $clinician = $this->Clinicians->patchEntity($clinician, $this->request->getData());
            if ($this->Clinicians->save($clinician)) {
                $this->Flash->success(__('The clinician has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The clinician could not be saved. Please, try again.'));
        }
        $users = $this->Clinicians->Users->find('list', limit: 200)->all();
        $patients = $this->Clinicians->Patients->find('list', limit: 200)->all();
        $this->set(compact('clinician', 'users', 'patients'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Clinician id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $clinician = $this->Clinicians->get($id, contain: ['Patients']);
        $this->Authorization->authorize($clinician);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $clinician = $this->Clinicians->patchEntity($clinician, $this->request->getData());
            if ($this->Clinicians->save($clinician)) {
                $this->Flash->success(__('The clinician has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The clinician could not be saved. Please, try again.'));
        }
        $users = $this->Clinicians->Users->find('list', limit: 200)->all();
        $patients = $this->Clinicians->Patients->find('list', limit: 200)->all();
        $this->set(compact('clinician', 'users', 'patients'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Clinician id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $clinician = $this->Clinicians->get($id);
        $this->Authorization->authorize($clinician);
        if ($this->Clinicians->delete($clinician)) {
            $this->Flash->success(__('The clinician has been deleted.'));
        } else {
            $this->Flash->error(__('The clinician could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
