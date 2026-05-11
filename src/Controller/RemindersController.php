<?php
// src/Controller/RemindersController.php

namespace App\Controller;

use App\Policy\UserAccess;
use Cake\Event\EventInterface;

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

        // Build query with scope applied
        $query = $this->Reminders->find()
            ->contain(['VitalsReminders', 'VitalData']);

        $query = $this->Authorization->applyScope($query, 'index')
            ->order(['Reminders.scheduled_at' => 'DESC']);

        $reminders = $query->all();

        // Get patient's reminder settings
        $settings = $this->fetchTable('ReminderSettings')->getPatientSettings($patientId);

        // Separate reminders by status
        $activeReminders = [];
        $completedReminders = [];
        $dismissedReminders = [];

        foreach ($reminders as $reminder) {
            if ($reminder->interaction_type === 'completed') {
                $completedReminders[] = $reminder;
            } elseif ($reminder->interaction_type === 'dismissed') {
                $dismissedReminders[] = $reminder;
            } else {
                $activeReminders[] = $reminder;
            }
        }

        $this->set(compact('activeReminders', 'completedReminders', 'dismissedReminders', 'settings'));
    }

    /**
     * Get active reminders for logged-in patient
     */
    public function active()
    {

    }

    /**
     * Dismiss a reminder
     */
    public function dismiss($id = null)
    {

    }

    /**
     * Get reminder count for badge
     */
    public function count()
    {

    }

    /**
     * Configure reminder settings
     */
    public function settings()
    {

    }
}
