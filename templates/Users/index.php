<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\User> $users
 */
$this->assign('title', 'Users Management');
// CSS
$this->start('datatables_css');
echo $this->Html->css([
    'https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css',
    'datatable-shared',
    'users'
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
$this->Html->script('users-index', ['block' => 'script']);

$identity = $this->Identity->get();

$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
    ['title' => 'Users', 'url' => null]
];
?>

<!-- Header Section -->
<?= $this->element('page_header', [
    'breadcrumbs' => $breadcrumbs
]) ?>
    <!-- Header Section -->
    <div class="dash-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="dash-title mb-1">User Management</h1>
            <p class="text-muted mb-0">Manage system users and their roles</p>
        </div>
        <div>
            <?= $this->Html->link(
                '<i class="bi bi-plus-circle"></i> New User',
                ['action' => 'add'],
                [
                    'class' => 'btn btn-primary',
                    'escape' => false
                ]
            ) ?>
        </div>
    </div>
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Total Users</div>
                <div class="stat-value"><?= count($users) ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Admins</div>
                <div class="stat-value stat-unread">
                    <?= collection($users)->countBy(fn($user) => $user->type === 'admin')->toArray()[true] ?? 0 ?>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Clinicians</div>
                <div class="stat-value stat-read">
                    <?= collection($users)->countBy(fn($user) => $user->type === 'clinician')->toArray()[true] ?? 0 ?>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Patients</div>
                <div class="stat-value stat-arch">
                    <?= collection($users)->countBy(fn($user) => $user->type === 'patient')->toArray()[true] ?? 0 ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-3">
        <button class="filter-btn active" data-filter="all">All Users</button>
        <button class="filter-btn admin" data-filter="admin">Admins</button>
        <button class="filter-btn clinician" data-filter="clinician">Clinicians</button>
        <button class="filter-btn patient" data-filter="patient">Patients</button>
    </div>

    <!-- Mobile Search (visible only on mobile) -->
    <div class="d-md-none mb-3">
        <input type="text" id="mobileSearch" class="form-control" placeholder="Search users...">
    </div>

    <!-- Desktop Table -->
    <div class="table-responsive d-none d-md-block">
        <table id="usersTable" class="table table-striped table-hover dt-hidden">
            <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Contact</th>
                <th>Type</th>
                <th>Date of Birth</th>
                <th>Registered</th>
                <th class="text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr data-user-type="<?= h($user->type) ?>">
                    <td class="align-middle">
                        <?= $this->Html->link(
                            '<span class="badge bg-secondary">#' . $this->Number->format($user->id) . '</span>',
                            ['action' => 'view', $user->id],
                            ['escape' => false, 'class' => 'text-decoration-none']
                        ) ?>
                    </td>
                    <td class="align-middle">
                        <?= $this->Html->link(
                            '<div class="fw-bold">' . h($user->first_name . ' ' . $user->last_name) . '</div><small class="text-muted">@' . h($user->username) . '</small>',
                            ['action' => 'view', $user->id],
                            ['escape' => false, 'class' => 'text-decoration-none']
                        ) ?>
                    </td>
                    <td class="align-middle">
                        <div>
                            <i class="bi bi-envelope"></i>
                            <a href="mailto:<?= h($user->email) ?>" class="email-link">
                                <?= h($user->email) ?>
                            </a>
                        </div>
                        <?php if ($user->phone): ?>
                            <div class="text-muted small mt-1">
                                <i class="bi bi-telephone"></i> <?= h($user->phone) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="align-middle" data-filter="<?= h($user->type) ?>">
                        <span class="badge badge-<?= strtolower($user->type) ?>">
                            <?= h(ucfirst($user->type)) ?>
                        </span>
                    </td>
                    <td class="align-middle nowrap">
                        <?= h($user->date_of_birth ? $user->date_of_birth->format('M d, Y') : '-') ?>
                    </td>
                    <td class="align-middle nowrap">
                        <div><?= h($user->created_at->format('M d, Y')) ?></div>
                        <small class="text-muted"><?= h($user->created_at->format('g:i A')) ?></small>
                    </td>
                    <td class="align-middle text-center nowrap">
                        <div class="btn-group" role="group">
                            <?= $this->Html->link(
                                '<i class="bi bi-eye"></i>',
                                ['action' => 'view', $user->id],
                                [
                                    'class' => 'btn btn-sm btn-outline-primary',
                                    'escape' => false,
                                    'title' => 'View'
                                ]
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="bi bi-pencil"></i>',
                                ['action' => 'edit', $user->id],
                                [
                                    'class' => 'btn btn-sm btn-outline-secondary',
                                    'escape' => false,
                                    'title' => 'Edit'
                                ]
                            ) ?>
<!--                            --><?php //= $this->Form->postLink(
//                                '<i class="bi bi-trash"></i>',
//                                ['action' => 'delete', $user->id],
//                                [
//                                    'class' => 'btn btn-sm btn-outline-danger',
//                                    'escape' => false,
//                                    'confirm' => __('Are you sure you want to delete {0}?', $user->username),
//                                    'title' => 'Delete'
//                                ]
//                            ) ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="d-md-none" id="mobileCardsContainer">
        <?php foreach ($users as $user): ?>
            <div class="sub-card"
                 data-user-type="<?= h($user->type) ?>"
                 data-search-text="<?= h(strtolower($user->first_name . ' ' . $user->last_name . ' ' . $user->username . ' ' . $user->email)) ?>">
                <div class="card-top">
                    <div>
                        <div class="card-name">
                            <?= h($user->first_name . ' ' . $user->last_name) ?>
                            <span class="badge bg-secondary ms-2">#<?= h($user->id) ?></span>
                        </div>
                        <div class="card-phone text-muted">@<?= h($user->username) ?></div>
                    </div>
                    <span class="badge badge-<?= strtolower($user->type) ?>">
                        <?= h(ucfirst($user->type)) ?>
                    </span>
                </div>
                <div class="card-field">
                    <strong><i class="bi bi-envelope"></i> Email:</strong>
                    <a href="mailto:<?= h($user->email) ?>"><?= h($user->email) ?></a>
                </div>
                <?php if ($user->phone): ?>
                    <div class="card-field">
                        <strong><i class="bi bi-telephone"></i> Phone:</strong>
                        <?= h($user->phone) ?>
                    </div>
                <?php endif; ?>
                <?php if ($user->date_of_birth): ?>
                    <div class="card-field">
                        <strong><i class="bi bi-calendar"></i> Date of Birth:</strong>
                        <?= h($user->date_of_birth->format('M d, Y')) ?>
                    </div>
                <?php endif; ?>
                <div class="card-date">
                    <i class="bi bi-clock-history"></i>
                    Registered: <?= h($user->created_at->format('M d, Y')) ?> at <?= h($user->created_at->format('g:i A')) ?>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <?= $this->Html->link(
                        '<i class="bi bi-eye"></i> View',
                        ['action' => 'view', $user->id],
                        ['class' => 'btn btn-sm btn-outline-primary flex-fill', 'escape' => false]
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="bi bi-pencil"></i> Edit',
                        ['action' => 'edit', $user->id],
                        ['class' => 'btn btn-sm btn-outline-secondary flex-fill', 'escape' => false]
                    ) ?>
<!--                    --><?php //= $this->Form->postLink(
//                        '<i class="bi bi-trash"></i> Delete',
//                        ['action' => 'delete', $user->id],
//                        [
//                            'class' => 'btn btn-sm btn-outline-danger flex-fill',
//                            'escape' => false,
//                            'confirm' => __('Delete {0}?', $user->username)
//                        ]
//                    ) ?>
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

<?php $this->end(); ?>
