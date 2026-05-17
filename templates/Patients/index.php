<?php
use App\Policy\UserAccess;

$this->assign('title', 'Patients Management');

$this->start('datatables_css');
echo $this->Html->css([
    'users-index'
]);
$this->end();

$identity = $this->request->getAttribute('identity');

$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
    ['title' => 'Patients', 'url' => null]
];

$totalPatients = count($patients);
?>

<?= $this->element('page_header', [
    'breadcrumbs' => $breadcrumbs
]) ?>

<div class="patients-page">

    <div class="patients-header">
        <div>
            <h1>Assigned Patients</h1>
            <p>Manage assigned patient accounts and records</p>
        </div>

        <?php if (UserAccess::isAdmin($identity)): ?>
            <?= $this->Html->link(
                '<i class="bi bi-people"></i> Assign Patients to Clinician',
                ['controller' => 'Patients', 'action' => 'assign'],
                [
                    'class' => 'patients-main-btn',
                    'escape' => false
                ]
            ) ?>
        <?php elseif (UserAccess::isClinician($identity)): ?>
            <?= $this->Html->link(
                '<i class="bi bi-people"></i> Manage My Patients',
                ['controller' => 'Patients', 'action' => 'assign', UserAccess::getClinicianId($identity)],
                [
                    'class' => 'patients-main-btn',
                    'escape' => false
                ]
            ) ?>
        <?php endif; ?>
    </div>

    <div class="patients-stats-grid">

        <div class="patients-stat-card">
            <div class="patients-stat-icon blue">
                <i class="bi bi-people"></i>
            </div>

            <div>
                <span>TOTAL PATIENTS</span>
                <h2><?= count($patients) ?></h2>
                <p>Assigned to you</p>
            </div>
        </div>

        <div class="patients-stat-card">
            <div class="patients-stat-icon green">
                <i class="bi bi-calendar-check"></i>
            </div>

            <div>
                <span>ACTIVE THIS WEEK</span>
                <h2><?= count($patients) ?></h2>
                <p>Had recent activity</p>
            </div>
        </div>

        <div class="patients-stat-card">
            <div class="patients-stat-icon orange">
                <i class="bi bi-clipboard2-pulse"></i>
            </div>

            <div>
                <span>TOTAL RECORDS</span>
                <h2><?= $totalRecords ?></h2>
                <p>Health records</p>
            </div>
        </div>

        <div class="patients-stat-card">
            <div class="patients-stat-icon purple">
                <i class="bi bi-heart-pulse"></i>
            </div>

            <div>
                <span>HIGH PRIORITY</span>
                <h2><?= $criticalPatients ?></h2>
                <p>Requiring attention</p>
            </div>
        </div>

    </div>

    <div class="patients-panel">

        <div class="patients-toolbar">
            <div class="patients-search">
                <i class="bi bi-search"></i>
                <input type="text" id="patientSearchInput" placeholder="Search patients by name, email or phone...">
            </div>
        </div>

        <div class="patients-table">

            <div class="patients-table-head">
                <div>Patient</div>
                <div>Contact</div>
                <div>Joined</div>
                <div>Actions</div>
            </div>

            <?php foreach ($patients as $patient): ?>
                <?php
                $user = $patient->user ?? null;

                $fullName = $user
                    ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))
                    : 'Patient #' . $patient->id;

                $initials = $user
                    ? strtoupper(substr($user->first_name ?? 'P', 0, 1) . substr($user->last_name ?? '', 0, 1))
                    : 'P';

                $email = $user->email ?? null;
                $phone = $user->phone ?? null;
                $username = $user->username ?? null;
                $createdAt = $user->created_at ?? null;
                ?>

                <div class="patient-row"
                     data-search="<?= h(strtolower($fullName . ' ' . ($email ?? '') . ' ' . ($phone ?? '') . ' ' . ($username ?? ''))) ?>">

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
                                <a href="mailto:<?= h($email) ?>"><?= h($email) ?></a>
                            <?php else: ?>
                                <span>—</span>
                            <?php endif; ?>
                        </div>

                        <div>
                            <i class="bi bi-telephone"></i>

                            <?php if ($phone): ?>
                                <a href="tel:<?= h($phone) ?>"><?= h($phone) ?></a>
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
                                'class' => 'patient-action-btn view',
                                'escape' => false,
                                'title' => 'View'
                            ]
                        ) ?>

                        <?= $this->Html->link(
                            '<i class="bi bi-pencil"></i>',
                            ['action' => 'edit', $patient->id],
                            [
                                'class' => 'patient-action-btn edit',
                                'escape' => false,
                                'title' => 'Edit'
                            ]
                        ) ?>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <div class="patients-footer">
            Showing <?= h($totalPatients) ?> patient<?= $totalPatients === 1 ? '' : 's' ?>
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
