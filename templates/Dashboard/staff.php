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
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => null]
];
?>

<!-- Header Section -->
<?= $this->element('page_header', [
    'breadcrumbs' => $breadcrumbs
]) ?>

<!-- Header Section -->
<!-- Hero Welcome Card -->
<div class="dashboard-hero mb-4">

    <div class="hero-left">

        <div class="hero-avatar">
            <?= $this->Html->image('admin.png', [
                'alt' => 'Doctor',
                'class' => 'hero-doctor-img'
            ]) ?>
        </div>

        <div class="hero-content">
            <h1 class="hero-title">
                Welcome back,
                <span class="hero-name">
            System Admin
        </span>
            </h1>

            <p class="hero-subtitle">
                Here’s what’s happening with your messages today.
            </p>

            <div class="hero-alert">
                <i class="bi bi-envelope-fill"></i>
                You have
                <strong><?= h($stats['unread']) ?> unread patient enquiries</strong>
            </div>

            <div class="hero-buttons">
                <?= $this->Html->link(
                    '<i class="bi bi-chat-dots-fill"></i> View Messages',
                    '#recentTable',
                    [
                        'class' => 'hero-btn primary',
                        'escape' => false
                    ]
                ) ?>

                <?= $this->Html->link(
                    '<i class="bi bi-person-lines-fill"></i> Patient Records',
                    ['controller' => 'Patients', 'action' => 'index'],
                    [
                        'class' => 'hero-btn secondary',
                        'escape' => false
                    ]
                ) ?>
            </div>

        </div>

    </div>

    <div class="hero-envelope">
        <i class="bi bi-envelope-paper-heart-fill"></i>

        <div class="hero-badge">
            <?= h($stats['unread']) ?>
        </div>
    </div>

</div>

<!-- Stats -->
<!-- Premium Stats Cards -->
<div class="stats-grid mb-4">

    <div class="premium-stat-card total">
        <div class="stat-top">
            <div class="stat-icon blue">
                <i class="bi bi-files"></i>
            </div>

            <div class="stat-info-top">
                <div class="premium-stat-label">Total Submissions</div>
            </div>
        </div>

        <div class="premium-stat-value">
            <?= h($stats['total']) ?>
        </div>

        <div class="mini-wave blue"></div>
    </div>

    <div class="premium-stat-card unread">
        <div class="stat-top">
            <div class="stat-icon red">
                <i class="bi bi-envelope"></i>
            </div>

            <div class="stat-info-top">
                <div class="premium-stat-label">Unread</div>
            </div>
        </div>

        <div class="premium-stat-value text-danger">
            <?= h($stats['unread']) ?>
        </div>

        <div class="mini-wave red"></div>
    </div>

    <div class="premium-stat-card read">
        <div class="stat-top">
            <div class="stat-icon green">
                <i class="bi bi-check-circle"></i>
            </div>

            <div class="stat-info-top">
                <div class="premium-stat-label">Read</div>
            </div>
        </div>

        <div class="premium-stat-value text-success">
            <?= h($stats['read']) ?>
        </div>

        <div class="mini-wave green"></div>
    </div>

    <div class="premium-stat-card archived">
        <div class="stat-top">
            <div class="stat-icon purple">
                <i class="bi bi-archive"></i>
            </div>

            <div class="stat-info-top">
                <div class="premium-stat-label">Archived</div>
            </div>
        </div>

        <div class="premium-stat-value text-secondary">
            <?= h($stats['archived']) ?>
        </div>

        <div class="mini-wave purple"></div>
    </div>

</div>

<div class="filters-bar mb-3">

    <div class="filters-left">

        <?= $this->Html->link(
            '<i class="bi bi-grid"></i> All',
            ['?' => ['status'=>'all']],
            [
                'class' => 'filter-btn ' . ($status==='all' ? 'active' : ''),
                'escape' => false
            ]
        ) ?>

        <?= $this->Html->link(
            '<i class="bi bi-circle-fill"></i> Unread',
            ['?' => ['status'=>'unread']],
            [
                'class' => 'filter-btn unread ' . ($status==='unread' ? 'active' : ''),
                'escape' => false
            ]
        ) ?>

        <?= $this->Html->link(
            '<i class="bi bi-check-circle"></i> Read',
            ['?' => ['status'=>'read']],
            [
                'class' => 'filter-btn read ' . ($status==='read' ? 'active' : ''),
                'escape' => false
            ]
        ) ?>

        <?= $this->Html->link(
            '<i class="bi bi-archive"></i> Archived',
            ['?' => ['status'=>'archived']],
            [
                'class' => 'filter-btn archived ' . ($status==='archived' ? 'active' : ''),
                'escape' => false
            ]
        ) ?>

    </div>

    <div class="filters-search">
        <i class="bi bi-search"></i>
        <input
            type="text"
            id="tableSearch"
            placeholder="Search patient messages..."
        >
    </div>

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
                                    'action'=>'staff'
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
                                'action'=>'staff'
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
