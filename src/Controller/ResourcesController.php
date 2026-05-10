<?php
declare(strict_types=1);

namespace App\Controller;

use App\Policy\UserAccess;

/**
 * Resources Controller
 *
 * @property \App\Model\Table\ResourcesTable $Resources
 */
class ResourcesController extends AppController
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
        $resource = $this->Resources->newEmptyEntity();

        // Check authorization
        if (!$this->Authorization->can($resource, 'index')) {
            $this->Flash->error(__('You do not have permission to access that page.'));
            return $this->redirect($this->getRedirectUrl());
        }

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

        // Check authorization
        if (!$this->Authorization->can($resource, 'view')) {
            $this->Flash->error(__('You do not have permission to view this resource.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $this->set(compact('resource'));
    }
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
//    public function add()
//    {
//        $resource = $this->Resources->newEmptyEntity();
//
//        // Check authorization
//        if (!$this->Authorization->can($resource, 'add')) {
//            $this->Flash->error(__('You do not have permission to add resources.'));
//            return $this->redirect($this->getRedirectUrl());
//        }
//
//        if ($this->request->is('post')) {
//            $resource = $this->Resources->patchEntity($resource, $this->request->getData());
//            if ($this->Resources->save($resource)) {
//                $this->Flash->success(__('The resource has been saved.'));
//                return $this->redirect(['action' => 'index']);
//            }
//            $this->Flash->error(__('The resource could not be saved. Please, try again.'));
//        }
//        $clinicians = $this->Resources->Clinicians->find('list', limit: 200)->all();
//        $this->set(compact('resource', 'clinicians'));
//    }

    public function add()
    {

        // bella: this 1 line below is temporary just for me to test, shld remove later on
        $this->Authorization->skipAuthorization();

        $resource = $this->Resources->newEmptyEntity();

        if ($this->request->is('post')) {

            $data = $this->request->getData();

            // set system-controlled fields
            $data['status'] = 'published';
            $data['published_at'] = date('Y-m-d H:i:s');

            $identity = $this->request->getAttribute('identity');
            $data['clinician_id'] = $identity->get('id');

            $resource = $this->Resources->patchEntity($resource, $data);

            if ($this->Resources->save($resource)) {
                $this->Flash->success(__('The resource has been saved.'));
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('The resource could not be saved. Please, try again.'));
        }

        $this->set(compact('resource'));
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

        // Check authorization
        if (!$this->Authorization->can($resource, 'edit')) {
            $this->Flash->error(__('You do not have permission to edit this resource.'));
            return $this->redirect($this->getRedirectUrl());
        }

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

        // Check authorization
        if (!$this->Authorization->can($resource, 'delete')) {
            $this->Flash->error(__('You do not have permission to delete this resource.'));
            return $this->redirect($this->getRedirectUrl());
        }

        if ($this->Resources->delete($resource)) {
            $this->Flash->success(__('The resource has been deleted.'));
        } else {
            $this->Flash->error(__('The resource could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
    /**
     * Browse method - Public-facing resources page for patients
     *
     * Displays published resources that patients can view.
     * Staff can see drafts and published; patients see only published.
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function browse()
    {
        $this->Authorization->skipAuthorization();
        $identity = $this->request->getAttribute('identity');
        // Base query for published resources
        $query = $this->Resources->find()
            ->contain(['Clinicians' => ['Users']]) // Include author info
            ->orderBy(['Resources.published_at' => 'DESC']);
        // If user is staff, they can see drafts too (optional)
        if ($identity && UserAccess::isStaff($identity)) {
            // Staff sees all resources
        } else {
            // Patients and public only see published
            $query->where(['Resources.status' => 'published']);
        }
        // Optional: Filter by type
        $type = $this->request->getQuery('type');
        if ($type && in_array($type, ['article', 'video', 'blog', 'link', 'news'])) {
            $query->where(['Resources.type' => $type]);
        }
        $resources = $this->paginate($query);
        $this->set(compact('resources'));
    }

    /**
     * Read method (patient view))
     *
     * @param string|null $id Resource id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function read($id = null)
    {
        $resource = $this->Resources->get($id, contain: ['Clinicians']);

        // Check authorization
        if (!$this->Authorization->can($resource, 'read')) {
            $this->Flash->error(__('You do not have permission to read this resource.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $this->set(compact('resource'));
    }
}
