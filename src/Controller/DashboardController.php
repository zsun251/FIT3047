<?php
declare(strict_types=1);

namespace App\Controller;

class DashboardController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();

        $this->viewBuilder()->setLayout('admin');
        $this->loadComponent('Flash');
    }

    public function beforeFilter(\Cake\Event\EventInterface $event): void
    {
        parent::beforeFilter($event);

        $user = $this->request->getAttribute('identity');

        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $usersTable = $this->fetchTable('Users');

        $fullUser = $usersTable->get($user->getIdentifier(), [
            'contain' => ['Admins', 'Clinicians', 'Patients']
        ]);

        // Block patients from dashboard
        if (!empty($fullUser->patients)) {
            $this->Flash->error('You are not authorised to access the dashboard.');
            $this->redirect('/');
        }
    }

    public function index(): void
    {
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
            ->limit(10)
            ->all();

        $this->set(compact('stats', 'recent', 'status'));
    }
}
