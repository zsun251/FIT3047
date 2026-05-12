<?php
declare(strict_types=1);

namespace App\Controller;

use App\Policy\UserAccess;
use Cake\Event\EventInterface;
use Cake\I18n\FrozenTime;

class RemindersController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->viewBuilder()->setLayout('app');
        $this->loadComponent('Flash');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        // Allow JSON requests for API endpoints
        if ($this->request->is('json') || $this->request->getParam('_ext') === 'json') {
            $this->viewBuilder()->setClassName('Json');
        }
    }

    /**
     * Index method - Display all reminders for logged-in patient
     */
    public function index()
    {
        $identity = $this->request->getAttribute('identity');
        $reminder = $this->Reminders->newEmptyEntity();

        // Check authorization
        if (!$this->Authorization->can($reminder, 'index')) {
            $this->Flash->error(__('You do not have permission to access reminders.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $patientId = UserAccess::getPatientId($identity);

        $now = FrozenTime::now();
        $endOfDay = $now->endOfDay();

        // Build query with scope applied
        $query = $this->Reminders->find()
            ->contain(['VitalsReminders', 'VitalData']);

        $query = $this->Authorization->applyScope($query, 'index')
            ->where([
                'Reminders.scheduled_at <=' => $endOfDay,
                'DATE(Reminders.scheduled_at)' => $now->format('Y-m-d')
            ])
            ->order(['Reminders.scheduled_at' => 'ASC']);

        $reminders = $query->all();

        // Get patient's reminder settings
        $settings = $this->fetchTable('ReminderSettings')->getPatientSettings($patientId);

        // Separate reminders by status
        $activeReminders = [];
        $upcomingReminders = [];
        $completedReminders = [];
        $dismissedReminders = [];

        foreach ($reminders as $reminder) {
            if ($reminder->interaction_type === 'completed') {
                $completedReminders[] = $reminder;
            } elseif ($reminder->interaction_type === 'dismissed') {
                $dismissedReminders[] = $reminder;
            } else {
                if ($reminder->scheduled_at > $now) {
                    $minutesUntil = (int) $reminder->scheduled_at->diffInMinutes($now, false);
                    $reminder->minutes_until = $minutesUntil;

                    $hoursUntil = floor($minutesUntil / 60);
                    $remainingMinutes = $minutesUntil % 60;
                    $reminder->hours_until = $hoursUntil;
                    $reminder->remaining_minutes = $remainingMinutes;

                    $upcomingReminders[] = $reminder;
                } else {
                    $activeReminders[] = $reminder;
                }
            }
        }

        // Filter completed reminders to only today
        $today = $now->format('Y-m-d');
        $completedTodayReminders = array_filter($completedReminders, function($r) use ($today) {
            return $r->interacted_at && $r->interacted_at->format('Y-m-d') === $today;
        });

        $this->set(compact('activeReminders', 'upcomingReminders', 'completedReminders', 'completedTodayReminders', 'dismissedReminders', 'settings'));
    }

    /**
     * Dismiss a reminder
     */
    public function dismiss($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        // Get the actual reminder entity
        $reminder = $this->Reminders->get($id);

        // Check authorization
        if (!$this->Authorization->can($reminder, 'dismiss')) {
            $this->Flash->error(__('You do not have permission to dismiss this reminder.'));
            return $this->redirect(['action' => 'index']);
        }

        $identity = $this->request->getAttribute('identity');
        $patientId = UserAccess::getPatientId($identity);

        // Dismiss the reminder
        if ($this->Reminders->dismissReminder($id, $patientId)) {
            $this->Flash->success(__('The reminder has been dismissed.'));
        } else {
            $this->Flash->error(__('The reminder could not be dismissed. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Get reminder count for badge (API)
     */
    public function count()
    {
        $this->request->allowMethod(['get']);

        $identity = $this->request->getAttribute('identity');

        if (!$identity || !UserAccess::isPatient($identity)) {
            $this->Authorization->skipAuthorization();
            $this->set([
                'count' => 0,
                '_serialize' => ['count']
            ]);
            return;
        }

        $reminder = $this->Reminders->newEmptyEntity();
        $this->Authorization->authorize($reminder, 'count');

        $patientId = UserAccess::getPatientId($identity);
        $count = $this->Reminders->getPendingCount($patientId);

        $this->set([
            'count' => $count,
            '_serialize' => ['count']
        ]);
    }

    /**
     * Configure reminder settings
     */
    public function settings()
    {
        $identity = $this->request->getAttribute('identity');

        // Redirect staff users
        if ($identity && UserAccess::isStaff($identity)) {
            $this->Authorization->skipAuthorization();
            $this->Flash->error(__('Only patients can access reminders.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'staff']);
        }

        $reminder = $this->Reminders->newEmptyEntity();
        $this->Authorization->authorize($reminder, 'settings');

        if (!$identity || !UserAccess::isPatient($identity)) {
            $this->Flash->error(__('You do not have permission to access this page.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        $patientId = UserAccess::getPatientId($identity);
        $reminderSettingsTable = $this->fetchTable('ReminderSettings');
        $settings = $reminderSettingsTable->getPatientSettings($patientId);

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if ($settings) {
                $settings = $reminderSettingsTable->patchEntity($settings, $data);
            } else {
                $data['patient_id'] = $patientId;
                $settings = $reminderSettingsTable->newEntity($data);
            }

            if ($reminderSettingsTable->save($settings)) {
                $this->Flash->success(__('Your reminder settings have been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The reminder settings could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('settings'));
    }

    /**
     * Helper to get redirect URL based on user type
     */
    protected function getRedirectUrl(): array
    {
        $identity = $this->request->getAttribute('identity');

        if (!$identity) {
            return ['controller' => 'Users', 'action' => 'login'];
        }

        if (UserAccess::isStaff($identity)) {
            return ['controller' => 'Dashboard', 'action' => 'staff'];
        }

        return ['controller' => 'Dashboard', 'action' => 'patient'];
    }
}
