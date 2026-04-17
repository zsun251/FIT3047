<?php
declare(strict_types=1);

namespace App\Controller;

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
        $this->Authorization->authorize($vitalDataEntity);
        $query = $this->VitalData->find()
            ->contain(['Patients']);
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
        $this->Authorization->authorize($vitalDataEntity);
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
        $this->Authorization->authorize($vitalDataEntity);
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
        $this->Authorization->authorize($vitalDataEntity);
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
        $this->Authorization->authorize($vitalDataEntity);
        if ($this->VitalData->delete($vitalDataEntity)) {
            $this->Flash->success(__('The vital data has been deleted.'));
        } else {
            $this->Flash->error(__('The vital data could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
