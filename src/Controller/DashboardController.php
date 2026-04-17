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





    public function staff(): void
    {
        $this->Authorization->authorize(
            new Page('dashboard', 'staff'),
            'access'
        );

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
        $this->Authorization->authorize(
            new Page('dashboard', 'patient'),
            'access'
        );

        $identity = $this->Authentication->getIdentity();

        $currentUser = $this->fetchTable('Users')->get($identity->getIdentifier(), [
            'contain' => ['Patients'],
        ]);

        $this->set(compact('currentUser'));
    }
}
