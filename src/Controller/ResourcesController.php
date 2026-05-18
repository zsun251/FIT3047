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
            ->contain(['Clinicians' => ['Users']])
            ->orderBy(['Resources.created_at' => 'DESC']);

        // Optional: Filter by type
        $type = $this->request->getQuery('type');
        if ($type && in_array($type, ['article', 'video', 'blog', 'link', 'news'])) {
            $query->where(['Resources.type' => $type]);
        }

        $resources = $this->paginate($query);

        // Stats (always global, ignoring the active type filter)
        $totalCount     = $this->Resources->find()->count();
        $publishedCount = $this->Resources->find()->where(['status' => 'published'])->count();
        $draftCount     = $this->Resources->find()->where(['status' => 'draft'])->count();
        $videoCount     = $this->Resources->find()->where(['type' => 'video'])->count();

        $this->set(compact('resources', 'totalCount', 'publishedCount', 'draftCount', 'videoCount'));
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
        $resource = $this->Resources->get($id, contain: ['Clinicians' => ['Users']]);

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
    public function add()
    {
        $resource = $this->Resources->newEmptyEntity();

        // Check authorization
        if (!$this->Authorization->can($resource, 'add')) {
            $this->Flash->error(__('You do not have permission to add resources.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $identity = $this->request->getAttribute('identity');
        $isAdmin = UserAccess::isAdmin($identity);

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Set system-controlled fields
            $data['status'] = 'published';
            $data['published_at'] = date('Y-m-d H:i:s');

            // Admin selects clinician from form; clinicians are auto-assigned
            if (!$isAdmin) {
                $data['clinician_id'] = UserAccess::getClinicianId($identity);
            }

            $resource = $this->Resources->patchEntity($resource, $data);

            if ($this->Resources->save($resource)) {
                $this->Flash->success(__('The resource has been saved.'));
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('The resource could not be saved. Please, try again.'));
        }

        // Admin needs the clinician list to select an author
        $clinicians = null;
        if ($isAdmin) {
            $clinicians = $this->Resources->Clinicians->find()
                ->contain(['Users'])
                ->orderBy(['Users.first_name' => 'ASC'])
                ->all();
        }

        $this->set(compact('resource', 'isAdmin', 'clinicians'));
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
        $resource = $this->Resources->get($id, contain: ['Clinicians' => ['Users']]);

        // Check authorization
        if (!$this->Authorization->can($resource, 'edit')) {
            $this->Flash->error(__('You do not have permission to edit this resource.'));
            return $this->redirect($this->getRedirectUrl());
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            unset($data['clinician_id'], $data['created_at'], $data['published_at']);

            $resource = $this->Resources->patchEntity($resource, $data);
            if ($this->Resources->save($resource)) {
                $this->Flash->success(__('The resource has been updated.'));
                return $this->redirect(['action' => 'view', $resource->id]);
            }
            $this->Flash->error(__('The resource could not be saved. Please try again.'));
        }

        $this->set(compact('resource'));
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
        $resource = $this->Resources->get($id, contain: ['Clinicians' => ['Users']]);

        // Check authorization
        if (!$this->Authorization->can($resource, 'read')) {
            $this->Flash->error(__('You do not have permission to read this resource.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $this->set(compact('resource'));
    }
}
