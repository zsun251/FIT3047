<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\RecaptchaService;

/**
 * ContactMessages Controller
 *
 * @property \App\Model\Table\ContactMessagesTable $ContactMessages
 */
class ContactMessagesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->viewBuilder()->setLayout('app');
        $this->loadComponent('Flash');
    }

    public function beforeFilter(\Cake\Event\EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions([
            'add'
        ]);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $contactMessage = $this->ContactMessages->newEmptyEntity();

        // Check authorization
        if (!$this->Authorization->can($contactMessage, 'index')) {
            $this->Flash->error(__('You do not have permission to access that page.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $query = $this->ContactMessages->find();
        $contactMessages = $this->paginate($query);
        $this->set(compact('contactMessages'));
    }

    /**
     * View method
     *
     * @param string|null $id Contact Message id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $contactMessage = $this->ContactMessages->get($id, contain: []);

        // Check authorization
        if (!$this->Authorization->can($contactMessage, 'view')) {
            $this->Flash->error(__('You do not have permission to view this contact message.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $this->set(compact('contactMessage'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // Use default.php instead of app.php
        $this->viewBuilder()->setLayout('default');
        $this->Authorization->skipAuthorization();

        $contactMessage = $this->ContactMessages->newEmptyEntity();

        // Prefill contact details for logged-in users
        $identity = $this->Authentication->getIdentity();

        if ($identity && !$this->request->is('post')) {
            $currentUser = $this->fetchTable('Users')->get($identity->getIdentifier());

            $contactMessage->first_name = $currentUser->first_name ?? '';
            $contactMessage->last_name = $currentUser->last_name ?? '';
            $contactMessage->email = $currentUser->email ?? '';
            $contactMessage->contact_number = $currentUser->phone ?? '';
        }

        if ($this->request->is('post')) {
            $recaptcha = new RecaptchaService();
            $token = $this->request->getData('g_recaptcha_response');
            if (!$recaptcha->verify($token, 'contact')) {
                $this->Flash->error('reCAPTCHA verification failed. Please try again.');
                $this->set(compact('contactMessage'));
                return;
            }
            // Process form
            $contactMessage = $this->ContactMessages->patchEntity(
                $contactMessage,
                $this->request->getData()
            );
            // Set server-side fields — never trust these from the form
            $contactMessage->status = 'unread';
            $contactMessage->submitted_at = new \DateTime();
            if ($this->ContactMessages->save($contactMessage)) {
                $this->Flash->success(__('Your message has been sent. We will be in touch with you soon.'));
                return $this->redirect('/');
            }
            $this->Flash->error('Please check the form and try again.');
        }
        $this->set(compact('contactMessage'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Contact Message id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $contactMessage = $this->ContactMessages->get($id, contain: []);

        // Check authorization
        if (!$this->Authorization->can($contactMessage, 'edit')) {
            $this->Flash->error(__('You do not have permission to edit this contact message.'));
            return $this->redirect($this->getRedirectUrl());
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $contactMessage = $this->ContactMessages->patchEntity($contactMessage, $this->request->getData());
            if ($this->ContactMessages->save($contactMessage)) {
                $this->Flash->success(__('The contact message has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The contact message could not be saved. Please, try again.'));
        }
        $this->set(compact('contactMessage'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Contact Message id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $contactMessage = $this->ContactMessages->get($id);

        // Check authorization
        if (!$this->Authorization->can($contactMessage, 'delete')) {
            $this->Flash->error(__('You do not have permission to delete this contact message.'));
            return $this->redirect($this->getRedirectUrl());
        }

        if ($this->ContactMessages->delete($contactMessage)) {
            $this->Flash->success(__('The contact message has been deleted.'));
        } else {
            $this->Flash->error(__('The contact message could not be deleted. Please, try again.'));
        }
        // Redirect to a custom URL if provided
        $redirectUrl = $this->request->getData('redirect');
        if ($redirectUrl) {
            return $this->redirect($redirectUrl);
        }
        return $this->redirect(['action' => 'index']);
    }

    public function updateStatus($id = null, $status = null)
    {
        // Only allow POST requests
        $this->request->allowMethod(['post']);

        // Allowed statuses (whitelist = security best practice)
        $allowed = ['read', 'unread', 'archived'];

        // Validate status
        if (!in_array($status, $allowed)) {
            $this->Flash->error('Invalid status.');
            return $this->redirect($this->referer());
        }

        // Get message
        $message = $this->ContactMessages->get($id);

        // Check authorization (using edit permission for status updates)
        if (!$this->Authorization->can($message, 'edit')) {
            $this->Flash->error(__('You do not have permission to update this contact message.'));
            return $this->redirect($this->getRedirectUrl());
        }

        // Update status
        $message->status = $status;

        // Save
        if ($this->ContactMessages->save($message)) {
            $this->Flash->success("Message marked as {$status}.");
        } else {
            $this->Flash->error('Unable to update message.');
        }
        return $this->redirect($this->referer());
    }
}
