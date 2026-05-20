<?php
declare(strict_types=1);

/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Patient>|array $patients
 * @var int|null $totalRecords
 * @var int|null $criticalPatients
 */

use App\Policy\UserAccess;

$this->assign('title', 'Patients Management');

$this->start('datatables_css');
echo $this->Html->css([
    'users-index',
]);
$this->end();

$identity = $this->request->getAttribute('identity');

$patients = $patients ?? [];
$patientList = is_array($patients) ? $patients : iterator_to_array($patients);

$totalPatients = count($patientList);
$totalRecords = $totalRecords ?? 0;
$criticalPatients = $criticalPatients ?? 0;

$backUrl = $this->request->referer(true);

if (!$backUrl || str_contains($backUrl, '/patients')) {
    $backUrl = ['controller' => 'Dashboard', 'action' => 'staff'];
}
?>

<div class="patients-page">

    <div class="rc-dashboard-page-header patients-page-header">
        <div class="rc-dashboard-header-main patients-header-main">
            <div class="rc-dashboard-header-copy patients-header-copy">
                <div class="rc-dashboard-title-row patients-title-row">
                    <?= $this->Html->link(
                        '<i class="bi bi-arrow-left"></i>',
                        $backUrl,
                        [
                            'class' => 'rc-back-button patients-back-button',
                            'escape' => false,
                            'aria-label' => __('Return to dashboard'),
                        ],
                    ) ?>

                    <h1 class="rc-dashboard-title">
                        <?= __('Assigned Patients') ?>
                    </h1>
                </div>

                <p class="rc-dashboard-subtitle">
                    <?= __('Manage assigned patient accounts and records') ?>
                </p>
            </div>

            <?php if (UserAccess::isAdmin($identity)): ?>
                <?= $this->Html->link(
                    '<i class="bi bi-people"></i><span>' . __('Assign Patients to Clinician') . '</span>',
                    ['controller' => 'Patients', 'action' => 'assign'],
                    [
                        'class' => 'rc-action-pill patients-main-button',
                        'escape' => false,
                    ],
                ) ?>
            <?php elseif (UserAccess::isClinician($identity)): ?>
                <?= $this->Html->link(
                    '<i class="bi bi-people"></i><span>' . __('Manage My Patients') . '</span>',
                    ['controller' => 'Patients', 'action' => 'assign', UserAccess::getClinicianId($identity)],
                    [
                        'class' => 'rc-action-pill patients-main-button',
                        'escape' => false,
                    ],
                ) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="patients-stats-grid">

        <div class="patients-stat-card">
            <div class="patients-stat-icon blue">
                <i class="bi bi-people"></i>
            </div>

            <div>
                <span><?= __('Total Patients') ?></span>
                <h2><?= h($totalPatients) ?></h2>
                <p><?= __('Assigned to you') ?></p>
            </div>
        </div>

        <div class="patients-stat-card">
            <div class="patients-stat-icon green">
                <i class="bi bi-calendar-check"></i>
            </div>

            <div>
                <span><?= __('Active This Week') ?></span>
                <h2><?= h($totalPatients) ?></h2>
                <p><?= __('Had recent activity') ?></p>
            </div>
        </div>

        <div class="patients-stat-card">
            <div class="patients-stat-icon orange">
                <i class="bi bi-clipboard2-pulse"></i>
            </div>

            <div>
                <span><?= __('Total Records') ?></span>
                <h2><?= h($totalRecords) ?></h2>
                <p><?= __('Health records') ?></p>
            </div>
        </div>

        <div class="patients-stat-card">
            <div class="patients-stat-icon purple">
                <i class="bi bi-heart-pulse"></i>
            </div>

            <div>
                <span><?= __('High Priority') ?></span>
                <h2><?= h($criticalPatients) ?></h2>
                <p><?= __('Requiring attention') ?></p>
            </div>
        </div>

    </div>

    <div class="patients-panel">

        <div class="patients-toolbar">
            <div class="patients-search">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    id="patientSearchInput"
                    placeholder="<?= h(__('Search patients by name, email or phone...')) ?>"
                >
            </div>
        </div>

        <div class="patients-table">

            <div class="patients-table-head">
                <div><?= __('Patient') ?></div>
                <div><?= __('Contact') ?></div>
                <div><?= __('Joined') ?></div>
                <div><?= __('Actions') ?></div>
            </div>

            <?php foreach ($patientList as $patient): ?>
                <?php
                $user = $patient->user ?? null;

                $firstName = $user ? trim((string)($user->first_name ?? '')) : '';
                $lastName = $user ? trim((string)($user->last_name ?? '')) : '';

                $fullName = trim($firstName . ' ' . $lastName);

                if ($fullName === '') {
                    $fullName = 'Patient #' . (int)$patient->id;
                }

                $initials = strtoupper(substr($firstName !== '' ? $firstName : 'P', 0, 1) . substr($lastName, 0, 1));
                $initials = substr($initials ?: 'P', 0, 2);

                $email = $user->email ?? null;
                $phone = $user->phone ?? null;
                $phoneHref = $phone ? preg_replace('/[^0-9+]/', '', (string)$phone) : null;
                $username = $user->username ?? null;
                $createdAt = $user->created_at ?? null;

                $searchText = strtolower(
                    $fullName . ' ' .
                    ($email ?? '') . ' ' .
                    ($phone ?? '') . ' ' .
                    ($username ?? '')
                );
                ?>

                <div
                    class="patient-row"
                    data-search="<?= h($searchText) ?>"
                >

                    <div class="patient-main">
                        <div class="patient-avatar">
                            <?= h($initials) ?>
                        </div>

                        <div>
                            <div class="patient-name">
                                <?= h($fullName) ?>
                            </div>

                            <div class="patient-username">
                                @<?= h($username ?? 'patient') ?>
                            </div>
                        </div>
                    </div>

                    <div class="patient-contact">
                        <div>
                            <i class="bi bi-envelope"></i>

                            <?php if ($email): ?>
                                <a href="mailto:<?= h($email) ?>">
                                    <?= h($email) ?>
                                </a>
                            <?php else: ?>
                                <span>—</span>
                            <?php endif; ?>
                        </div>

                        <div>
                            <i class="bi bi-telephone"></i>

                            <?php if ($phone && $phoneHref): ?>
                                <a href="tel:<?= h($phoneHref) ?>">
                                    <?= h($phone) ?>
                                </a>
                            <?php else: ?>
                                <span>—</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="patient-created">
                        <?php if ($createdAt): ?>
                            <strong><?= h($createdAt->format('M d, Y')) ?></strong>
                            <span><?= h($createdAt->format('g:i A')) ?></span>
                        <?php else: ?>
                            <span>—</span>
                        <?php endif; ?>
                    </div>

                    <div class="patient-actions">
                        <?= $this->Html->link(
                            '<i class="bi bi-eye"></i>',
                            ['action' => 'view', $patient->id],
                            [
                                'class' => 'rc-icon-action patient-action-btn view',
                                'escape' => false,
                                'title' => __('View'),
                                'aria-label' => __('View patient'),
                            ],
                        ) ?>

                        <?= $this->Html->link(
                            '<i class="bi bi-pencil"></i>',
                            ['action' => 'edit', $patient->id],
                            [
                                'class' => 'rc-icon-action rc-icon-action-green patient-action-btn edit',
                                'escape' => false,
                                'title' => __('Edit'),
                                'aria-label' => __('Edit patient'),
                            ],
                        ) ?>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <div class="patients-footer">
            <?= __('Showing {0} patient{1}', h($totalPatients), $totalPatients === 1 ? '' : 's') ?>
        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('patientSearchInput');
        const rows = document.querySelectorAll('.patient-row');

        if (!searchInput) {
            return;
        }

        searchInput.addEventListener('input', function () {
            const value = this.value.toLowerCase();

            rows.forEach(function (row) {
                const text = row.dataset.search || '';
                row.style.display = text.includes(value) ? 'grid' : 'none';
            });
        });
    });
</script>
