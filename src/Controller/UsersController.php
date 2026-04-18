<?php
declare(strict_types=1);

namespace App\Controller;

use App\Policy\UserAccess;
use Cake\Http\Exception\ForbiddenException;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
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
            'login',
            'forgotPassword',
            'resetPassword'
        ]);
    }

    public function login()
    {
        // Use default.php instead of app.php
        $this->viewBuilder()->setLayout('default');
        $this->Authorization->skipAuthorization();

        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();

        if ($result && $result->isValid()) {
            $identity = $this->Authentication->getIdentity();

            $target = match (true) {
                UserAccess::isPatient($identity) => [
                    'controller' => 'Dashboard',
                    'action' => 'patient',
                ],

                UserAccess::isStaff($identity) => [
                    'controller' => 'Dashboard',
                    'action' => 'staff',
                ],

                default => [
                    'controller' => 'Users',
                    'action' => 'index',
                ],
            };

            return $this->redirect($target);
        }

        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error(__('Invalid username or password'));
        }
    }

    public function logout()
    {
        // Use default.php instead of app.php
        $this->viewBuilder()->setLayout('default');
        $this->Authorization->skipAuthorization();

        $result = $this->Authentication->getResult();
        // regardless of POST or GET, redirect if user is logged in
        if ($result && $result->isValid()) {
            $this->Authentication->logout();

            $this->Flash->success(__('Logged out successfully.'));
            return $this->redirect(['controller' => 'Pages', 'action' => 'display']);
        }
    }

    public function forgotPassword()
    {
        // Use default.php instead of app.php
        $this->viewBuilder()->setLayout('default');
        $this->Authorization->skipAuthorization();
        $this->request->allowMethod(['get', 'post']);

        if ($this->request->is('post')) {
            $email = trim((string)$this->request->getData('email'));

            if ($email !== '') {
                $this->Flash->success('If an account with that email exists, password reset instructions would be sent.');
            } else {
                $this->Flash->error('Please enter your email address.');
            }
        }
    }

    public function resetPassword()
    {
        // Use default.php instead of app.php
        $this->viewBuilder()->setLayout('default');
        $this->Authorization->skipAuthorization();

        $this->request->allowMethod(['get', 'post']);

        if ($this->request->is('post')) {
            $this->Flash->success('Password reset functionality is not implemented in this version.');
            return $this->redirect(['action' => 'login']);
        }
    }

    public function index()
    {
        $user = $this->Users->newEmptyEntity();

        // Check authorization
        if (!$this->Authorization->can($user, 'index')) {
            $this->Flash->error(__('You do not have permission to access that page.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $query = $this->Users->find();
        $users = $this->paginate($query);

        $this->set(compact('users'));
    }

    public function view($id = null)
    {
        $user = $this->Users->get($id, contain: ['Admins', 'Clinicians', 'Patients']);

        // Check authorization
        if (!$this->Authorization->can($user, 'view')) {
            $this->Flash->error(__('You do not have permission to view this user.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $this->set(compact('user'));
    }

    public function add()
    {
        $user = $this->Users->newEmptyEntity();

        // Check authorization
        if (!$this->Authorization->can($user, 'add')) {
            $this->Flash->error(__('You do not have permission to add users.'));
            return $this->redirect($this->getRedirectUrl());
        }

        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    public function edit($id = null)
    {
        $user = $this->Users->get($id, contain: []);

        // Check authorization
        if (!$this->Authorization->can($user, 'edit')) {
            $this->Flash->error(__('You do not have permission to edit this user.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $identity = $this->Authentication->getIdentity();
        $isPatientSelfEdit = $identity && UserAccess::isPatient($identity) && (int)$identity->getIdentifier() === (int)$id;

        if ($identity && UserAccess::isPatient($identity) && !$isPatientSelfEdit) {
            throw new ForbiddenException('You can only edit your own account.');
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($isPatientSelfEdit) {
                $allowedFields = [
                    'username',
                    'password',
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    'address',
                    'date_of_birth',
                ];
                $user = $this->Users->patchEntity($user, $this->request->getData(), ['fields' => $allowedFields]);

            } else {
                $user = $this->Users->patchEntity($user, $this->request->getData());

            }
            if ($this->Users->save($user)) {
                $this->Flash->success(__('Your information has been saved.'));

                if ($isPatientSelfEdit) {
                    return $this->redirect([
                        'controller' => 'Dashboard',
                        'action' => 'patient',
                    ]);
                }

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Your information could not be saved. Please, try again.'));

        }
        $this->set(compact('user', 'isPatientSelfEdit'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);

        // Check authorization
        if (!$this->Authorization->can($user, 'delete')) {
            $this->Flash->error(__('You do not have permission to delete this user.'));
            return $this->redirect($this->getRedirectUrl());
        }

        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
