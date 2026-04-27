<?php
declare(strict_types=1);

use App\Policy\UserAccess;

/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Appointment> $appointments
 */

$identity = $this->request->getAttribute('identity');
$isAdmin = $identity && UserAccess::isAdmin($identity);

$this->assign('title', $isAdmin ? 'Appointments' : 'Assigned Appointments');

// ============================================================================
// DATATABLE ASSETS
// ============================================================================
$this->start('datatables_css');
echo $this->Html->css([
    'https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css',
]);
$this->end();

$this->start('datatables_js');
echo $this->Html->script([
    'https://cdn.datatables.net/2.1.8/js/dataTables.min.js',
    'https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js',
]);
$this->end();

$this->Html->css([
    'datatable-shared',
    'appointments',
], ['block' => 'css']);

$this->Html->script('mobile-datatable', ['block' => 'script']);
$this->Html->script('appointments-staff', ['block' => 'script']);

$appointmentList = is_array($appointments) ? $appointments : iterator_to_array($appointments);

$normalizeStatus = function (?string $status): string {
    $value = strtolower(trim((string)$status));

    return match ($value) {
        'scheduled', 'confirmed' => 'upcoming',
        'canceled' => 'cancelled',
        '' => 'unknown',
        default => $value,
    };
};

$getPatientLabel = function ($appointment): string {
    if (!empty($appointment->patient) && !empty($appointment->patient->user)) {
        $firstName = trim((string)($appointment->patient->user->first_name ?? ''));
        $lastName = trim((string)($appointment->patient->user->last_name ?? ''));
        $fullName = trim($firstName . ' ' . $lastName);

        if ($fullName !== '') {
            return $fullName;
        }
    }

    if (!empty($appointment->patient_id)) {
        return 'Patient #' . (int)$appointment->patient_id;
    }

    return 'Patient';
};

$getClinicianLabel = function ($appointment): string {
    if (!empty($appointment->clinician) && !empty($appointment->clinician->user)) {
        $firstName = trim((string)($appointment->clinician->user->first_name ?? ''));
        $lastName = trim((string)($appointment->clinician->user->last_name ?? ''));
        $fullName = trim($firstName . ' ' . $lastName);

        if ($fullName !== '') {
            return $fullName;
        }
    }

    if (!empty($appointment->clinician_id)) {
        return 'Clinician #' . (int)$appointment->clinician_id;
    }

    return 'Clinician';
};

$getDateLabel = function ($dateTime): string {
    if (!$dateTime) {
        return 'No appointment date';
    }

    return $dateTime->format('d M Y');
};

$getTimeLabel = function ($dateTime): string {
    if (!$dateTime) {
        return '—';
    }

    return $dateTime->format('g:i A');
};

$getStatusLabel = function (?string $status): string {
    $value = strtolower(trim((string)$status));

    return match ($value) {
        'upcoming', 'scheduled', 'confirmed' => 'UPCOMING',
        'completed' => 'COMPLETED',
        'cancelled', 'canceled' => 'CANCELLED',
        '' => 'UNKNOWN',
        default => strtoupper(str_replace('_', ' ', $value)),
    };
};

$stats = [
    'total' => count($appointmentList),
    'upcoming' => 0,
    'completed' => 0,
    'cancelled' => 0,
];

foreach ($appointmentList as $appointment) {
    $normalizedStatus = $normalizeStatus($appointment->status ?? null);

    if (array_key_exists($normalizedStatus, $stats)) {
        $stats[$normalizedStatus]++;
    }
}
?>

    <div class="dash-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="dash-title mb-0"><?= $isAdmin ? __('Appointments') : __('Assigned Appointments') ?></h1>
        </div>
        <div>
            <?= $this->Html->link(
                '<i class="bi bi-plus-circle"></i> Create Appointment',
                ['action' => 'add'],
                [
                    'class' => 'btn btn-primary',
                    'escape' => false,
                ]
            ) ?>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Total Appointments</div>
                <div class="stat-value"><?= h($stats['total']) ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Upcoming</div>
                <div class="stat-value stat-upcoming"><?= h($stats['upcoming']) ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Completed</div>
                <div class="stat-value stat-completed"><?= h($stats['completed']) ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Cancelled</div>
                <div class="stat-value stat-cancelled"><?= h($stats['cancelled']) ?></div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <button class="filter-btn all active" data-filter="all">All</button>
        <button class="filter-btn upcoming" data-filter="upcoming">Upcoming</button>
        <button class="filter-btn completed" data-filter="completed">Completed</button>
        <button class="filter-btn cancelled" data-filter="cancelled">Cancelled</button>
    </div>

    <div class="d-md-none mb-3">
        <input type="text" id="mobileSearch" class="form-control" placeholder="Search appointments...">
    </div>

<?php if (!empty($appointmentList)): ?>
    <div class="table-responsive d-none d-md-block">
        <table id="appointmentsTable" class="table table-striped table-hover dt-hidden">
            <thead>
            <tr>
                <th>ID</th>
                <th>Patient</th>
                <th>Clinician</th>
                <th>Appointment Time</th>
                <th>Duration</th>
                <th>Status</th>
                <th class="text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($appointmentList as $appointment): ?>
                <?php $normalizedStatus = $normalizeStatus($appointment->status ?? null); ?>
                <tr data-appointment-status="<?= h($normalizedStatus) ?>">
                    <td class="align-middle" data-order="<?= h((int)$appointment->id) ?>">
                        <span class="badge bg-secondary">#<?= h($appointment->id) ?></span>
                    </td>
                    <td class="align-middle">
                        <div class="name-cell"><?= h($getPatientLabel($appointment)) ?></div>
                        <div class="phone-text">Patient #<?= h($appointment->patient_id) ?></div>
                    </td>
                    <td class="align-middle">
                        <div class="name-cell"><?= h($getClinicianLabel($appointment)) ?></div>
                        <div class="phone-text">Clinician #<?= h($appointment->clinician_id) ?></div>
                    </td>
                    <td class="align-middle nowrap" data-order="<?= h($appointment->scheduled_at ? $appointment->scheduled_at->format('Y-m-d H:i:s') : '') ?>">
                        <?= h($getDateLabel($appointment->scheduled_at ?? null)) ?>
                        <div class="submitted-time"><?= h($getTimeLabel($appointment->scheduled_at ?? null)) ?></div>
                    </td>
                    <td class="align-middle nowrap" data-order="<?= h((int)($appointment->duration ?? 0)) ?>">
                        <?= !empty($appointment->duration) ? h($appointment->duration) . ' min' : '—' ?>
                    </td>
                    <td class="align-middle">
                        <span class="appointment-status appointment-status-<?= h($normalizedStatus === 'unknown' ? 'default' : $normalizedStatus) ?>">
                            <?= h($getStatusLabel($appointment->status ?? null)) ?>
                        </span>
                    </td>
                    <td class="align-middle nowrap text-center">
                        <?= $this->Html->link(
                            '<i class="bi bi-eye"></i>',
                            ['action' => 'view', $appointment->id],
                            [
                                'class' => 'btn-action-icon view me-1',
                                'escape' => false,
                                'title' => 'View',
                            ]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="bi bi-pencil"></i>',
                            ['action' => 'edit', $appointment->id],
                            [
                                'class' => 'btn-action-icon edit me-1',
                                'escape' => false,
                                'title' => 'Edit',
                            ]
                        ) ?>
                        <?= $this->Form->postLink(
                            '<i class="bi bi-trash"></i>',
                            ['action' => 'delete', $appointment->id],
                            [
                                'method' => 'delete',
                                'class' => 'btn-action-icon danger',
                                'escape' => false,
                                'confirm' => __('Are you sure you want to delete appointment #{0}?', $appointment->id),
                                'title' => 'Delete',
                            ]
                        ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="d-md-none" id="mobileCardsContainer">
        <?php foreach ($appointmentList as $appointment): ?>
            <?php $normalizedStatus = $normalizeStatus($appointment->status ?? null); ?>
            <div class="sub-card"
                 data-appointment-status="<?= h($normalizedStatus) ?>"
                 data-search-text="<?= h(strtolower(
                     $getPatientLabel($appointment) . ' ' .
                     $getClinicianLabel($appointment) . ' ' .
                     ($appointment->status ?? '') . ' ' .
                     ($appointment->id ?? '')
                 )) ?>">
                <div class="card-top">
                    <div>
                        <div class="card-name"><?= h($getPatientLabel($appointment)) ?></div>
                        <div class="card-phone"><?= h($getClinicianLabel($appointment)) ?></div>
                    </div>
                    <span class="appointment-status appointment-status-<?= h($normalizedStatus === 'unknown' ? 'default' : $normalizedStatus) ?>">
                        <?= h($getStatusLabel($appointment->status ?? null)) ?>
                    </span>
                </div>

                <div class="card-field">
                    <strong>Appointment Time:</strong>
                    <div>
                        <?= h($getDateLabel($appointment->scheduled_at ?? null)) ?>
                        at
                        <?= h($getTimeLabel($appointment->scheduled_at ?? null)) ?>
                    </div>
                </div>

                <div class="card-field">
                    <strong>Duration:</strong>
                    <div><?= !empty($appointment->duration) ? h($appointment->duration) . ' min' : 'Not provided' ?></div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <?= $this->Html->link(
                        '<i class="bi bi-eye"></i> View',
                        ['action' => 'view', $appointment->id],
                        ['class' => 'btn btn-sm btn-outline-primary flex-fill', 'escape' => false]
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="bi bi-pencil"></i> Edit',
                        ['action' => 'edit', $appointment->id],
                        ['class' => 'btn btn-sm btn-outline-secondary flex-fill', 'escape' => false]
                    ) ?>
                    <?= $this->Form->postLink(
                        '<i class="bi bi-trash"></i> Delete',
                        ['action' => 'delete', $appointment->id],
                        [
                            'method' => 'delete',
                            'class' => 'btn btn-sm btn-outline-danger flex-fill',
                            'escape' => false,
                            'confirm' => __('Are you sure you want to delete appointment #{0}?', $appointment->id),
                        ]
                    ) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

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
<?php else: ?>
    <div class="appointments-empty-state">
        <div class="appointments-empty-icon">
            <i class="bi bi-calendar-x"></i>
        </div>
        <h3><?= __('No appointments found') ?></h3>
        <p>
            <?php if ($isAdmin): ?>
                <?= __('There are currently no appointment records to display.') ?>
            <?php else: ?>
                <?= __('There are currently no appointment records assigned to you.') ?>
            <?php endif; ?>
        </p>
    </div>
<?php endif; ?>
