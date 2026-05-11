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
     * Route: /reminders
     */
    public function index()
    {

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
