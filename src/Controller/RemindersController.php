<?php
// src/Controller/RemindersController.php

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
            'Reminders.scheduled_at <=' => $endOfDay, // Show all reminders up to end of today
            'DATE(Reminders.scheduled_at)' => $now->format('Y-m-d') // Only today's reminders
        ])
        ->order(['Reminders.scheduled_at' => 'ASC']); // Show earliest first

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
            // Check if it's upcoming or active
            if ($reminder->scheduled_at > $now) {
                // Calculate minutes until reminder time
                $minutesUntil = (int) $reminder->scheduled_at->diffInMinutes($now, false);
                $reminder->minutes_until = $minutesUntil;

                // Also calculate hours for display
                $hoursUntil = floor($minutesUntil / 60);
                $remainingMinutes = $minutesUntil % 60;
                $reminder->hours_until = $hoursUntil;
                $reminder->remaining_minutes = $remainingMinutes;

                $upcomingReminders[] = $reminder;
            } else {
                // Time has passed - it's active now
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
