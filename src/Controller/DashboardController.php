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

    public function patient()
    {
        $page = new Page('dashboard', 'patient');

        // Check authorization
        if (!$this->Authorization->can($page, 'access')) {
            $this->Flash->error(__('You do not have permission to access that page.'));
            return $this->redirect($this->getRedirectUrl());
        }

        $identity = $this->Authentication->getIdentity();

        $currentUser = $this->fetchTable('Users')->get($identity->getIdentifier(), [
            'contain' => ['Patients'],
        ]);

        $this->set(compact('currentUser'));
    }
}
