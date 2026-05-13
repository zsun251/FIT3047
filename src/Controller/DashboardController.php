<?php
declare(strict_types=1);

namespace App\Controller;
use App\Policy\Resource\Page;
use App\Policy\UserAccess;

class DashboardController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();

        $this->viewBuilder()->setLayout('app');
        $this->loadComponent('Flash');
    }





    public function staff()
    {
        $page = new Page('dashboard', 'staff');

        // Check authorization
        if (!$this->Authorization->can($page, 'access')) {
            $this->Flash->error(__('You do not have permission to access that page.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $contactMessages = $this->fetchTable('ContactMessages');

        $status = $this->request->getQuery('status') ?? 'all';

        $allowed = ['all', 'unread', 'read', 'archived'];

        if (!in_array($status, $allowed)) {
            $status = 'all';
        }

        $stats = [
            'total' => $contactMessages->find()->count(),
            'unread' => $contactMessages->find()->where(['status' => 'unread'])->count(),
            'read' => $contactMessages->find()->where(['status' => 'read'])->count(),
            'archived' => $contactMessages->find()->where(['status' => 'archived'])->count(),
        ];

        $query = $contactMessages->find();

        if ($status !== 'all') {
            $query->where([
                'ContactMessages.status' => $status
            ]);
        }

        $recent = $query
            ->orderBy([
                'ContactMessages.submitted_at' => 'DESC'
            ])
            ->all();

        $this->set(compact('stats', 'recent', 'status'));
    }

    public function patient(): void
    {
        // Authorize access to the patient dashboard
        $this->Authorization->authorize(new Page('dashboard', 'patient'), 'access');

        // Load the currently logged-in user and linked patient record
        $identity = $this->Authentication->getIdentity();

        $currentUser = $this->fetchTable('Users')->get(
            $identity->getIdentifier(),
            contain: ['Patients'],
        );

        $patient = $currentUser->patient ?? null;
        $patientId = $patient?->id;

        $latestVital = null;
        $nextAppointment = null;
        $latestResource = null;
        $activeReminders = [];
        $reminderCount = 0;

        if ($patientId) {
            // Fetch the latest submitted vital data and linked symptoms
            $latestVital = $this->fetchTable('VitalData')->find()
                ->contain(['Symptoms'])
                ->where(['VitalData.patient_id' => $patientId])
                ->orderBy(['VitalData.recorded_at' => 'DESC'])
                ->first();

            // Fetch the next upcoming appointment for the patient
            $nextAppointment = $this->fetchTable('Appointments')->find()
                ->where([
                    'Appointments.patient_id' => $patientId,
                    'Appointments.scheduled_at >=' => new \Cake\I18n\FrozenTime(),
                ])
                ->orderBy(['Appointments.scheduled_at' => 'ASC'])
                ->first();

            // Fetch active reminders for the patient
            $remindersTable = $this->fetchTable('Reminders');
            $now = \Cake\I18n\FrozenTime::now();

            $activeReminders = $remindersTable->find()
                ->innerJoinWith('VitalsReminders')
                ->where([
                    'VitalsReminders.patient_id' => $patientId,
                    'Reminders.scheduled_at <=' => $now,
                    'Reminders.interaction_type IS' => null
                ])
                ->orderBy(['Reminders.scheduled_at' => 'DESC'])
                ->limit(3) // Show only 3 most recent
                ->all();

            // Get total count of active reminders
            $reminderCount = $remindersTable->getPendingCount($patientId);
        }

        // Fetch the latest published resource to preview on the dashboard
        $latestResource = $this->fetchTable('Resources')->find()
            ->where(['Resources.status' => 'published'])
            ->orderBy(['Resources.published_at' => 'DESC'])
            ->first();

        $this->set(compact(
            'currentUser',
            'latestVital',
            'nextAppointment',
            'latestResource',
            'activeReminders',
            'reminderCount'
        ));
    }
}
