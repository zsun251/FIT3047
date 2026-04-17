<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Resources Controller
 *
 * @property \App\Model\Table\ResourcesTable $Resources
 */
class ResourcesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $resource = $this->Resources->newEmptyEntity();
        $this->Authorization->authorize($resource);
        $query = $this->Resources->find()
            ->contain(['Clinicians']);
        $resources = $this->paginate($query);

        $this->set(compact('resources'));
    }

    /**
     * View method
     *
     * @param string|null $id Resource id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $resource = $this->Resources->get($id, contain: ['Clinicians']);
        $this->Authorization->authorize($resource);
        $this->set(compact('resource'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $resource = $this->Resources->newEmptyEntity();
        $this->Authorization->authorize($resource);
        if ($this->request->is('post')) {
            $resource = $this->Resources->patchEntity($resource, $this->request->getData());
            if ($this->Resources->save($resource)) {
                $this->Flash->success(__('The resource has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The resource could not be saved. Please, try again.'));
        }
        $clinicians = $this->Resources->Clinicians->find('list', limit: 200)->all();
        $this->set(compact('resource', 'clinicians'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Resource id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $resource = $this->Resources->get($id, contain: []);
        $this->Authorization->authorize($resource);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $resource = $this->Resources->patchEntity($resource, $this->request->getData());
            if ($this->Resources->save($resource)) {
                $this->Flash->success(__('The resource has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The resource could not be saved. Please, try again.'));
        }
        $clinicians = $this->Resources->Clinicians->find('list', limit: 200)->all();
        $this->set(compact('resource', 'clinicians'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Resource id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $resource = $this->Resources->get($id);
        $this->Authorization->authorize($resource);
        if ($this->Resources->delete($resource)) {
            $this->Flash->success(__('The resource has been deleted.'));
        } else {
            $this->Flash->error(__('The resource could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
