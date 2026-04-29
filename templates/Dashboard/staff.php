<?php
$this->assign('title','Dashboard');

// CSS
$this->start('datatables_css');
echo $this->Html->css([
    'https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css',
    'datatable-shared',
    'dashboard-staff'
]);
$this->end();

// JS
$this->start('datatables_js');
echo $this->Html->script([
    'https://cdn.datatables.net/2.1.8/js/dataTables.min.js',
    'https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js',
]);
$this->end();

// Page-specific JS
$this->Html->script('mobile-datatable', ['block' => 'script']);
$this->Html->script('dashboard-staff', ['block' => 'script']);

$user = $this->Identity->get();
?>

<!-- Header Section -->
<div class="dash-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="dash-title mb-1">
            Welcome, <em><?= h($user->first_name . ' ' . $user->last_name) ?></em>
        </h1>
        <p class="text-muted mb-0">Manage contact messages submissions and inquiries</p>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Total Submissions</div>
            <div class="stat-value"><?= h($stats['total']) ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Unread</div>
            <div class="stat-value stat-unread"><?= h($stats['unread']) ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Read</div>
            <div class="stat-value stat-read"><?= h($stats['read']) ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Archived</div>
            <div class="stat-value stat-arch"><?= h($stats['archived']) ?></div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="mb-3">
    <?= $this->Html->link('All', ['?' => ['status'=>'all']], [
        'class' => 'filter-btn ' . ($status==='all' ? 'active' : ''),
        'data-filter' => 'all'
    ]) ?>
    <?= $this->Html->link('Unread', ['?' => ['status'=>'unread']], [
        'class' => 'filter-btn unread ' . ($status==='unread' ? 'active' : ''),
        'data-filter' => 'unread'
    ]) ?>
    <?= $this->Html->link('Read', ['?' => ['status'=>'read']], [
        'class' => 'filter-btn read ' . ($status==='read' ? 'active' : ''),
        'data-filter' => 'read'
    ]) ?>
    <?= $this->Html->link('Archived', ['?' => ['status'=>'archived']], [
        'class' => 'filter-btn archived ' . ($status==='archived' ? 'active' : ''),
        'data-filter' => 'archived'
    ]) ?>
</div>

<!-- Mobile Search (visible only on mobile) -->
<div class="d-md-none mb-3">
    <input type="text" id="mobileSearch" class="form-control" placeholder="Search messages...">
</div>

<!-- Desktop Table -->
<div class="table-responsive d-none d-md-block">
    <table id="recentTable" class="table table-striped dt-hidden">
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Message</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($recent as $msg): ?>
            <tr>
                <td class="nowrap name-cell">
                    <?= h($msg->full_name) ?>
                    <?php if ($msg->has_phone): ?>
                        <div class="phone-text"><?= h($msg->contact_number) ?></div>
                    <?php endif; ?>
                </td>
                <td class="nowrap">
                    <a href="mailto:<?= h($msg->email) ?>" class="email-link">
                        <?= h($msg->email) ?>
                    </a>
                </td>
                <td>
                    <div class="msg-text"><?= h($msg->message) ?></div>
                </td>
                <td>
                        <span class="badge-status <?= $msg->status_badge_class ?>">
                            <?= h($msg->status) ?>
                        </span>
                </td>
                <td class="nowrap">
                    <?= h($msg->formatted_date) ?>
                    <div class="submitted-time"><?= h($msg->formatted_time) ?></div>
                </td>
                <td class="nowrap">
                    <?= $this->Html->link(
                        '<i class="bi bi-eye"></i>',
                        ['controller'=>'ContactMessages','action'=>'view',$msg->id],
                        [
                            'class'=>'btn-action-icon view me-1',
                            'escape' => false,
                            'title' => 'View'
                        ]
                    ) ?>
                    <?php if ($msg->status === 'unread'): ?>
                        <?= $this->Form->postLink(
                            '<i class="bi bi-check-circle"></i>',
                            ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id,'read'],
                            [
                                'class'=>'btn-action-icon mark-read me-1',
                                'escape' => false,
                                'title' => 'Mark as Read'
                            ]
                        ) ?>
                    <?php endif; ?>
                    <?php if ($msg->status === 'read'): ?>
                        <?= $this->Form->postLink(
                            '<i class="bi bi-archive"></i>',
                            ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id,'archived'],
                            [
                                'class'=>'btn-action-icon archive me-1',
                                'escape' => false,
                                'title' => 'Archive'
                            ]
                        ) ?>
                        <?= $this->Form->postLink(
                            '<i class="bi bi-arrow-counterclockwise"></i>',
                            ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id,'unread'],
                            [
                                'class'=>'btn-action-icon undo-read me-1',
                                'escape' => false,
                                'title' => 'Mark as Unread'
                            ]
                        ) ?>
                    <?php endif; ?>
                    <?php if ($msg->status === 'archived'): ?>
                        <?= $this->Form->postLink(
                            '<i class="bi bi-arrow-counterclockwise"></i>',
                            ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id,'read'],
                            [
                                'class'=>'btn-action-icon undo-archive me-1',
                                'escape' => false,
                                'title' => 'Unarchive'
                            ]
                        ) ?>
                    <?php endif; ?>
                    <?= $this->Form->postLink(
                        '<i class="bi bi-trash"></i>',
                        ['controller'=>'ContactMessages','action'=>'delete',$msg->id],
                        [
                            'class'=>'btn-action-icon danger',
                            'escape' => false,
                            'confirm'=>'Delete this submission?',
                            'title' => 'Delete',
                            'data' => [
                                'redirect' => $this->Url->build([
                                    'controller'=>'Dashboard',
                                    'action'=>'index'
                                ])
                            ]
                        ]
                    ) ?>
                </td>            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Mobile Cards -->
<div class="d-md-none" id="mobileCardsContainer">
    <?php foreach ($recent as $msg): ?>
        <div class="sub-card"
             data-status="<?= h($msg->status) ?>"
             data-search-text="<?= h(strtolower($msg->full_name . ' ' . $msg->email . ' ' . $msg->message)) ?>">
            <div class="card-top">
                <div>
                    <div class="card-name"><?= h($msg->full_name) ?></div>
                    <?php if ($msg->has_phone): ?>
                        <div class="card-phone"><?= h($msg->contact_number) ?></div>
                    <?php endif; ?>
                </div>
                <span class="badge-status <?= $msg->status_badge_class ?>">
                    <?= h($msg->status) ?>
                </span>
            </div>
            <div class="card-field">
                <strong>Email:</strong>
                <a href="mailto:<?= h($msg->email) ?>"><?= h($msg->email) ?></a>
            </div>
            <div class="card-field">
                <strong>Message:</strong>
                <div><?= h($msg->message) ?></div>
            </div>
            <div class="card-date">
                <?= h($msg->formatted_date) ?> at <?= h($msg->formatted_time) ?>
            </div>

            <!-- Mobile Action Buttons -->
            <div class="mt-3 d-flex flex-wrap gap-2">
                <?= $this->Html->link(
                    '<i class="bi bi-eye"></i> View',
                    ['controller'=>'ContactMessages','action'=>'view',$msg->id],
                    [
                        'class'=>'btn btn-sm btn-outline-primary',
                        'escape' => false
                    ]
                ) ?>

                <?php if ($msg->status === 'unread'): ?>
                    <?= $this->Form->postLink(
                        '<i class="bi bi-check-circle"></i> Mark Read',
                        ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id,'read'],
                        [
                            'class'=>'btn btn-sm btn-outline-success',
                            'escape' => false
                        ]
                    ) ?>
                <?php endif; ?>

                <?php if ($msg->status === 'read'): ?>
                    <?= $this->Form->postLink(
                        '<i class="bi bi-archive"></i> Archive',
                        ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id,'archived'],
                        [
                            'class'=>'btn btn-sm btn-outline-secondary',
                            'escape' => false
                        ]
                    ) ?>
                    <?= $this->Form->postLink(
                        '<i class="bi bi-arrow-counterclockwise"></i> Undo',
                        ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id,'unread'],
                        [
                            'class'=>'btn btn-sm btn-outline-danger',
                            'escape' => false
                        ]
                    ) ?>
                <?php endif; ?>

                <?php if ($msg->status === 'archived'): ?>
                    <?= $this->Form->postLink(
                        '<i class="bi bi-arrow-counterclockwise"></i> Unarchive',
                        ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id,'read'],
                        [
                            'class'=>'btn btn-sm btn-outline-success',
                            'escape' => false
                        ]
                    ) ?>
                <?php endif; ?>

                <?= $this->Form->postLink(
                    '<i class="bi bi-trash"></i> Delete',
                    ['controller'=>'ContactMessages','action'=>'delete',$msg->id],
                    [
                        'class'=>'btn btn-sm btn-outline-danger',
                        'escape' => false,
                        'confirm'=>'Delete this submission?',
                        'data' => [
                            'redirect' => $this->Url->build([
                                'controller'=>'Dashboard',
                                'action'=>'index'
                            ])
                        ]
                    ]
                ) ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Mobile Pagination -->
<div class="d-md-none mt-3" id="mobilePagination">
    <div class="d-flex justify-content-between align-items-center">
        <button id="mobilePrevBtn" class="btn btn-sm btn-outline-secondary" disabled>
            <i class="bi bi-chevron-left"></i> Previous
        </button>
        <span id="mobilePageInfo" class="text-muted small"></span>
        <button id="mobileNextBtn" class="btn btn-sm btn-outline-secondary">
            Next <i class="bi bi-chevron-right"></i>
        </button>
    </div>
</div>
