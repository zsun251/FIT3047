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
            'resetPassword',
            'register',
        ]);
    }

    public function register()
    {
        $this->viewBuilder()->setLayout('default');
        $this->Authorization->skipAuthorization();
        $this->request->allowMethod(['get', 'post']);

        $user = $this->Users->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['type'] = 'patient';

            $user = $this->Users->patchEntity($user, $data);

            $patientsTable = $this->fetchTable('Patients');
            $connection = $this->Users->getConnection();

            $success = $connection->transactional(function () use ($patientsTable, $user) {
                if (!$this->Users->save($user)) {
                    return false;
                }
                $patient = $patientsTable->newEntity(['user_id' => $user->id]);
                return (bool)$patientsTable->save($patient);
            });

            if ($success) {
                $this->Flash->success(__('Account created successfully. Please log in.'));
                return $this->redirect(['action' => 'login']);
            }

            $this->Flash->error(__('Registration failed. Please check the form and try again.'));
        }

        $this->set(compact('user'));
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

    /**
     * Forgot Password - Request reset email
     */
    public function forgotPassword()
    {
        $this->viewBuilder()->setLayout('default');
        $this->Authorization->skipAuthorization();
        $this->request->allowMethod(['get', 'post']);

        if ($this->request->is('post')) {
            $email = $this->request->getData('email');

            $user = $this->Users->find()
                ->where(['email' => $email])
                ->first();

            if ($user) {
                // Generate secure token
                $token = bin2hex(random_bytes(32));
                $user->reset_token = $token;
                $user->reset_token_expires = new \Cake\I18n\DateTime('+1 hour');

                if ($this->Users->save($user, ['validate' => false])) {
                    $this->sendResetEmail($user);
                    $this->Flash->success('Password reset instructions have been sent to your email.');
                    // For development: show the link directly
                    // Generate reset URL
                    $resetUrl = \Cake\Routing\Router::url([
                        'controller' => 'Users',
                        'action' => 'resetPassword',
                        $user->reset_token
                    ], true);

                    // For development: show the link directly
                    $this->Flash->success(
                        'Password reset link (development mode): <a href="' . $resetUrl . '">' . $resetUrl . '</a>',
                        ['escape' => false]
                    );
                } else {
                    $this->Flash->error('Unable to process your request. Please try again.');
                }
            } else {
                // Don't reveal if email exists or not (security best practice)
                $this->Flash->success('If that email exists, password reset instructions have been sent.');
            }

            return $this->redirect(['action' => 'login']);
        }
    }

    /**
     * Reset Password - Actually reset with token
     */
    public function resetPassword($token = null)
    {
        $this->viewBuilder()->setLayout('default');
        $this->Authorization->skipAuthorization();
        $this->request->allowMethod(['get', 'post']);

        // Try to get token from URL parameter if not passed as argument
        if (!$token) {
            $token = $this->request->getParam('token');
        }

        // Also try from pass array
        if (!$token) {
            $pass = $this->request->getParam('pass');
            $token = $pass[0] ?? null;
        }

        if (!$token) {
            $this->Flash->error('Invalid password reset link.');
            return $this->redirect(['action' => 'login']);
        }

        $user = $this->Users->find()
            ->where(['reset_token' => $token])
            ->first();

        // Check if user exists and token hasn't expired
        if (!$user || $user->reset_token_expires < new \Cake\I18n\DateTime()) {
            $this->Flash->error('Invalid or expired password reset link.');
            return $this->redirect(['action' => 'forgotPassword']);
        }

        if ($this->request->is('post')) {
            $password = $this->request->getData('password');
            $confirmPassword = $this->request->getData('confirm_password');

            if ($password !== $confirmPassword) {
                $this->Flash->error('Passwords do not match.');
            } else {
                $user->password = $password;
                $user->reset_token = null;
                $user->reset_token_expires = null;

                if ($this->Users->save($user)) {
                    $this->Flash->success('Your password has been reset. You can now log in.');
                    return $this->redirect(['action' => 'login']);
                } else {
                    $this->Flash->error('Unable to reset password. Please try again.');
                }
            }
        }

        $this->set(compact('token', 'user'));
    }

    /**
     * Send password reset email
     */
    private function sendResetEmail($user)
    {
        $mailer = new \Cake\Mailer\Mailer('default');

        $resetUrl = \Cake\Routing\Router::url([
            'controller' => 'Users',
            'action' => 'resetPassword',
            $user->reset_token
        ], true);

        $mailer
            ->setTo($user->email)
            ->setSubject('Reset Your RespiraCare Password')
            ->setEmailFormat('html')
            ->viewBuilder()
            ->setTemplate('password_reset')
            ->setLayout('default');

        $mailer->setViewVars([
            'user' => $user,
            'resetUrl' => $resetUrl
        ]);

        try {
            $mailer->deliver();
        } catch (\Exception $e) {
            // Log error but don't reveal to user
            \Cake\Log\Log::error('Failed to send password reset email: ' . $e->getMessage());
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

        // Get type-specific IDs
        $adminId = $user->admin ? $user->admin->id : null;
        $clinicianId = $user->clinician ? $user->clinician->id : null;
        $patientId = $user->patient ? $user->patient->id : null;

        $this->set(compact('user', 'adminId', 'clinicianId', 'patientId'));
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
