<?php
declare(strict_types=1);

use App\Policy\UserAccess;

$identity = $this->request->getAttribute('identity');
$isAdmin = $identity && UserAccess::isAdmin($identity);

$this->assign('title', $isAdmin ? 'Appointments' : 'Assigned Appointments');

$this->Html->css([
    'appointments',
], ['block' => 'css']);

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
            return 'Dr. ' . $fullName;
        }
    }

    if (!empty($appointment->clinician_id)) {
        return 'Clinician #' . (int)$appointment->clinician_id;
    }

    return 'Clinician';
};

$getDateLabel = function ($dateTime): string {
    return $dateTime ? $dateTime->format('d M Y') : 'No date';
};

$getTimeLabel = function ($dateTime): string {
    return $dateTime ? $dateTime->format('g:i A') : '—';
};

$getStatusLabel = function (?string $status): string {
    $value = strtolower(trim((string)$status));

    return match ($value) {
        'upcoming', 'scheduled', 'confirmed' => 'Upcoming',
        'completed' => 'Completed',
        'cancelled', 'canceled' => 'Cancelled',
        '' => 'Unknown',
        default => ucwords(str_replace('_', ' ', $value)),
    };
};

$getInitials = function (string $name): string {
    $words = preg_split('/\s+/', trim($name));
    $initials = '';

    foreach ($words as $word) {
        if ($word !== '') {
            $initials .= strtoupper(substr($word, 0, 1));
        }
    }

    return substr($initials ?: 'P', 0, 2);
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

$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
    ['title' => 'Appointments', 'url' => null]
];
?>

<?= $this->element('page_header', [
    'breadcrumbs' => $breadcrumbs
]) ?>

<div class="appointments-admin-page">

    <div class="admin-appointments-hero">
        <div>
            <h1><?= $isAdmin ? __('Appointments') : __('Assigned Appointments') ?></h1>
            <p>Manage upcoming and completed patient consultations</p>
        </div>

        <?= $this->Html->link(
            '<i class="bi bi-plus-circle"></i> Create Appointment',
            ['action' => 'add'],
            [
                'class' => 'admin-appointment-create-btn',
                'escape' => false,
            ]
        ) ?>
    </div>

    <div class="admin-appointment-stats-grid">

        <div class="admin-appointment-stat-card total">
            <div class="admin-appointment-stat-icon">
                <i class="bi bi-calendar2-week"></i>
            </div>

            <div>
                <span>Total Appointments</span>
                <h2><?= h($stats['total']) ?></h2>
                <p>Overall records</p>
            </div>
        </div>

        <div class="admin-appointment-stat-card upcoming">
            <div class="admin-appointment-stat-icon">
                <i class="bi bi-clock"></i>
            </div>

            <div>
                <span>Upcoming</span>
                <h2><?= h($stats['upcoming']) ?></h2>
                <p>Next visits</p>
            </div>
        </div>

        <div class="admin-appointment-stat-card completed">
            <div class="admin-appointment-stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>

            <div>
                <span>Completed</span>
                <h2><?= h($stats['completed']) ?></h2>
                <p>Successfully finished</p>
            </div>
        </div>

        <div class="admin-appointment-stat-card cancelled">
            <div class="admin-appointment-stat-icon">
                <i class="bi bi-x-circle"></i>
            </div>

            <div>
                <span>Cancelled</span>
                <h2><?= h($stats['cancelled']) ?></h2>
                <p>Missed or cancelled</p>
            </div>
        </div>

    </div>

    <div class="admin-appointments-panel">

        <div class="admin-appointments-toolbar">

            <div class="admin-appointment-tabs">
                <button type="button" class="admin-appointment-filter active" data-filter="all">All</button>
                <button type="button" class="admin-appointment-filter" data-filter="upcoming">Upcoming</button>
                <button type="button" class="admin-appointment-filter" data-filter="completed">Completed</button>
                <button type="button" class="admin-appointment-filter" data-filter="cancelled">Cancelled</button>
            </div>

            <div class="admin-appointment-search">
                <i class="bi bi-search"></i>
                <input type="text" id="appointmentSearch" placeholder="Search appointments...">
            </div>

        </div>

        <?php if (!empty($appointmentList)): ?>

            <div class="admin-appointment-list" id="appointmentList">

                <?php foreach ($appointmentList as $appointment): ?>

                    <?php
                    $normalizedStatus = $normalizeStatus($appointment->status ?? null);
                    $statusClass = $normalizedStatus === 'unknown' ? 'default' : $normalizedStatus;

                    $patientName = $getPatientLabel($appointment);
                    $clinicianName = $getClinicianLabel($appointment);

                    $searchText = strtolower(
                        $patientName . ' ' .
                        $clinicianName . ' ' .
                        ($appointment->status ?? '') . ' ' .
                        ($appointment->id ?? '') . ' ' .
                        ($appointment->patient_id ?? '') . ' ' .
                        ($appointment->clinician_id ?? '')
                    );
                    ?>

                    <div class="admin-appointment-card <?= h($statusClass) ?>"
                         data-status="<?= h($statusClass) ?>"
                         data-search="<?= h($searchText) ?>">

                        <div class="admin-appointment-avatar">
                            <?= h($getInitials($patientName)) ?>
                        </div>

                        <div class="admin-appointment-person">
                            <h3><?= h($patientName) ?></h3>
                            <p>Patient #<?= h($appointment->patient_id) ?></p>
                        </div>

                        <div class="admin-appointment-info">
                            <i class="bi bi-calendar-event"></i>

                            <div>
                                <strong><?= h($getDateLabel($appointment->scheduled_at ?? null)) ?></strong>
                                <span><?= h($getTimeLabel($appointment->scheduled_at ?? null)) ?></span>
                            </div>
                        </div>

                        <div class="admin-appointment-info">
                            <i class="bi bi-person"></i>

                            <div>
                                <strong><?= h($clinicianName) ?></strong>
                                <span>Clinician #<?= h($appointment->clinician_id) ?></span>
                            </div>
                        </div>

                        <div class="admin-appointment-info duration">
                            <i class="bi bi-clock"></i>

                            <div>
                                <strong>
                                    <?= !empty($appointment->duration)
                                        ? h($appointment->duration) . ' min'
                                        : '—' ?>
                                </strong>
                            </div>
                        </div>

                        <div>
                            <span class="admin-appointment-status admin-appointment-status-<?= h($statusClass) ?>">
                                <?= h($getStatusLabel($appointment->status ?? null)) ?>
                            </span>
                        </div>

                        <div class="admin-appointment-actions">

                            <?= $this->Html->link(
                                '<i class="bi bi-eye"></i>',
                                ['action' => 'view', $appointment->id],
                                [
                                    'class' => 'admin-appointment-action view',
                                    'escape' => false,
                                    'title' => 'View',
                                ]
                            ) ?>

                            <?= $this->Html->link(
                                '<i class="bi bi-pencil"></i>',
                                ['action' => 'edit', $appointment->id],
                                [
                                    'class' => 'admin-appointment-action edit',
                                    'escape' => false,
                                    'title' => 'Edit',
                                ]
                            ) ?>

                            <?= $this->Form->postLink(
                                '<i class="bi bi-trash"></i>',
                                ['action' => 'delete', $appointment->id],
                                [
                                    'method' => 'delete',
                                    'class' => 'admin-appointment-action delete',
                                    'escape' => false,
                                    'confirm' => __('Are you sure you want to delete appointment #{0}?', $appointment->id),
                                    'title' => 'Delete',
                                ]
                            ) ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <div class="admin-appointments-footer">
                <span>
                    Showing <?= count($appointmentList) ?>
                    appointment<?= count($appointmentList) === 1 ? '' : 's' ?>
                </span>
            </div>

        <?php else: ?>

            <div class="admin-appointments-empty-state">

                <div class="admin-appointments-empty-icon">
                    <i class="bi bi-calendar-x"></i>
                </div>

                <h3><?= __('No appointments found') ?></h3>

                <p>
                    <?= $isAdmin
                        ? __('There are currently no appointment records to display.')
                        : __('There are currently no appointment records assigned to you.') ?>
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const filters = document.querySelectorAll('.admin-appointment-filter');
        const cards = document.querySelectorAll('.admin-appointment-card');
        const searchInput = document.getElementById('appointmentSearch');

        let activeFilter = 'all';

        function applyFilters() {

            const searchValue = searchInput
                ? searchInput.value.toLowerCase().trim()
                : '';

            cards.forEach(function (card) {

                const status = card.dataset.status;
                const searchText = card.dataset.search || '';

                const statusMatch =
                    activeFilter === 'all' || status === activeFilter;

                const searchMatch =
                    searchText.includes(searchValue);

                card.style.display =
                    statusMatch && searchMatch
                        ? 'grid'
                        : 'none';
            });
        }

        filters.forEach(function (button) {

            button.addEventListener('click', function () {

                filters.forEach(function (btn) {
                    btn.classList.remove('active');
                });

                button.classList.add('active');

                activeFilter = button.dataset.filter;

                applyFilters();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }

    });
</script>
