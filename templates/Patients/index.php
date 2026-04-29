<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Patient> $patients
 */

use App\Policy\UserAccess;

$this->assign('title', 'Patients Management');

// CSS
$this->start('datatables_css');
echo $this->Html->css([
    'https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css',
    'datatable-shared',
    'users-index'
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
$this->Html->script('patients-index', ['block' => 'script']);

$identity = $this->request->getAttribute('identity');
?>

<!-- Header Section -->
<div class="dash-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="dash-title mb-1">Assigned Patients</h1>
        <p class="text-muted mb-0">Manage assigned patient accounts and records</p>
    </div>
    <div>
        <?php if (UserAccess::isAdmin($identity)): ?>
            <?= $this->Html->link(
                '<i class="bi bi-people"></i> Assign Patients to Clinician',
                ['controller' => 'Patients', 'action' => 'assign'],
                [
                    'class' => 'btn btn-outline-primary',
                    'escape' => false
                ]
            ) ?>
        <?php elseif (UserAccess::isClinician($identity)): ?>
            <?= $this->Html->link(
                '<i class="bi bi-people"></i> Manage My Patients',
                ['controller' => 'Patients', 'action' => 'assign', UserAccess::getClinicianId($identity)],
                [
                    'class' => 'btn btn-outline-primary',
                    'escape' => false
                ]
            ) ?>
        <?php endif; ?>
    </div>
<!--    TODO Patients are created automatically via schema-->
<!--    <div>-->
<!--        --><?php //if (UserAccess::isAdmin($identity)): ?>
<!--            --><?php //= $this->Html->link(
//                '<i class="bi bi-plus-circle"></i> New Patient',
//                ['action' => 'add'],
//                [
//                    'class' => 'btn btn-primary',
//                    'escape' => false
//                ]
//            ) ?>
<!--        --><?php //endif; ?>
<!--    </div>-->
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Total Patients</div>
            <div class="stat-value"><?= count($patients) ?></div>
        </div>
    </div>
</div>

<!-- Mobile Search (visible only on mobile) -->
<div class="d-md-none mb-3">
    <input type="text" id="mobileSearch" class="form-control" placeholder="Search patients...">
</div>

<!-- Desktop Table -->
<div class="table-responsive d-none d-md-block">
    <table id="patientsTable" class="table table-striped table-hover dt-hidden">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Created</th>
            <th class="text-center">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($patients as $patient): ?>
            <tr>
                <td class="align-middle">
                    <?= $this->Html->link(
                        '<span class="badge bg-secondary">#' . $this->Number->format($patient->id) . '</span>',
                        ['action' => 'view', $patient->id],
                        ['escape' => false, 'class' => 'text-decoration-none']
                    ) ?>
                </td>
                <td class="align-middle">
                    <?php if ($patient->hasValue('user')): ?>
                        <?= $this->Html->link(
                            '<div class="fw-bold">' . h($patient->user->first_name . ' ' . $patient->user->last_name) . '</div><small class="text-muted">@' . h($patient->user->username) . '</small>',
                            ['action' => 'view', $patient->id],
                            ['escape' => false, 'class' => 'text-decoration-none']
                        ) ?>
                    <?php else: ?>
                        <span class="text-muted">N/A</span>
                    <?php endif; ?>
                </td>
                <td class="align-middle">
                    <?php if ($patient->hasValue('user') && $patient->user->email): ?>
                        <a href="mailto:<?= h($patient->user->email) ?>" class="text-decoration-none">
                            <?= h($patient->user->email) ?>
                        </a>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
                <td class="align-middle">
                    <?php if ($patient->hasValue('user') && $patient->user->phone): ?>
                        <a href="tel:<?= h($patient->user->phone) ?>" class="text-decoration-none">
                            <?= h($patient->user->phone) ?>
                        </a>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
                <td class="align-middle nowrap">
                    <?php if ($patient->hasValue('user')): ?>
                        <div><?= h($patient->user->created_at->format('M d, Y')) ?></div>
                        <small class="text-muted"><?= h($patient->user->created_at->format('g:i A')) ?></small>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
                <td class="align-middle text-center nowrap">
                    <div class="btn-group" role="group">
                        <?= $this->Html->link(
                            '<i class="bi bi-eye"></i>',
                            ['action' => 'view', $patient->id],
                            [
                                'class' => 'btn btn-sm btn-outline-primary',
                                'escape' => false,
                                'title' => 'View'
                            ]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="bi bi-pencil"></i>',
                            ['action' => 'edit', $patient->id],
                            [
                                'class' => 'btn btn-sm btn-outline-secondary',
                                'escape' => false,
                                'title' => 'Edit'
                            ]
                        ) ?>
<!--                        --><?php //if (UserAccess::isAdmin($identity)): ?>
<!--                            --><?php //= $this->Form->postLink(
//                                '<i class="bi bi-trash"></i>',
//                                ['action' => 'delete', $patient->id],
//                                [
//                                    'class' => 'btn btn-sm btn-outline-danger',
//                                    'escape' => false,
//                                    'confirm' => __('Are you sure you want to delete patient #{0}?', $patient->id),
//                                    'title' => 'Delete'
//                                ]
//                            ) ?>
<!--                        --><?php //endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Mobile Cards -->
<div class="d-md-none" id="mobileCardsContainer">
    <?php foreach ($patients as $patient): ?>
        <div class="sub-card"
             data-search-text="<?= h(strtolower(($patient->user->first_name ?? '') . ' ' . ($patient->user->last_name ?? '') . ' ' . ($patient->user->username ?? '') . ' ' . ($patient->user->email ?? ''))) ?>">
            <div class="card-top">
                <div>
                    <?php if ($patient->hasValue('user')): ?>
                        <div class="card-name"><?= h($patient->user->first_name . ' ' . $patient->user->last_name) ?></div>
                        <div class="card-phone text-muted">@<?= h($patient->user->username) ?></div>
                    <?php else: ?>
                        <div class="card-name">Patient #<?= h($patient->id) ?></div>
                    <?php endif; ?>
                </div>
                <span class="badge bg-secondary">Patient</span>
            </div>

            <?php if ($patient->hasValue('user')): ?>
                <div class="card-field">
                    <strong><i class="bi bi-envelope"></i> Email:</strong>
                    <?php if ($patient->user->email): ?>
                        <a href="mailto:<?= h($patient->user->email) ?>"><?= h($patient->user->email) ?></a>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </div>

                <div class="card-field">
                    <strong><i class="bi bi-telephone"></i> Phone:</strong>
                    <?php if ($patient->user->phone): ?>
                        <a href="tel:<?= h($patient->user->phone) ?>"><?= h($patient->user->phone) ?></a>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </div>

                <div class="card-date">
                    <i class="bi bi-calendar"></i>
                    Joined: <?= h($patient->user->created_at->format('M d, Y')) ?>
                </div>
            <?php endif; ?>

            <div class="mt-3 d-flex gap-2">
                <?= $this->Html->link(
                    '<i class="bi bi-eye"></i> View',
                    ['action' => 'view', $patient->id],
                    ['class' => 'btn btn-sm btn-outline-primary flex-fill', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="bi bi-pencil"></i> Edit',
                    ['action' => 'edit', $patient->id],
                    ['class' => 'btn btn-sm btn-outline-secondary flex-fill', 'escape' => false]
                ) ?>
<!--                --><?php //if (UserAccess::isAdmin($identity)): ?>
<!--                    --><?php //= $this->Form->postLink(
//                        '<i class="bi bi-trash"></i> Delete',
//                        ['action' => 'delete', $patient->id],
//                        [
//                            'class' => 'btn btn-sm btn-outline-danger flex-fill',
//                            'escape' => false,
//                            'confirm' => __('Delete patient #{0}?', $patient->id)
//                        ]
//                    ) ?>
<!--                --><?php //endif; ?>
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
