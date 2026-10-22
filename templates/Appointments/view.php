<?php
declare(strict_types=1);

use App\Policy\UserAccess;

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Appointment $appointment
 */

$identity = $this->request->getAttribute('identity');
$isPatient = $identity && UserAccess::isPatient($identity);
$isStaff = $identity && UserAccess::isStaff($identity);

$this->assign('title', $isPatient ? 'My Appointment' : 'Appointment Details');

echo $this->Html->css(['form-shared', 'appointments',], ['block' => true]);

$statusClass = function (?string $status): string {
    $value = strtolower(trim((string)$status));

    return match ($value) {
        'upcoming', 'scheduled', 'confirmed' => 'appointment-status-upcoming',
        'completed' => 'appointment-status-completed',
        'cancelled', 'canceled' => 'appointment-status-cancelled',
        default => 'appointment-status-default',
    };
};

$statusLabel = function (?string $status): string {
    $status = trim((string)$status);

    if ($status === '') {
        return 'Unknown';
    }

    return ucwords(str_replace('_', ' ', strtolower($status)));
};

$formatDateTime = function ($dateTime): string {
    if (!$dateTime) {
        return 'Not scheduled';
    }

    return $dateTime->format('d M Y, g:i A');
};

$getPatientName = function ($appointment): string {
    if (!empty($appointment->patient) && !empty($appointment->patient->user)) {
        $firstName = trim((string)($appointment->patient->user->first_name ?? ''));
        $lastName = trim((string)($appointment->patient->user->last_name ?? ''));
        $fullName = trim($firstName . ' ' . $lastName);

        if ($fullName !== '') {
            return $fullName;
        }
    }

    return 'Patient #' . (int)$appointment->patient_id;
};

$getClinicianName = function ($appointment): string {
    if (!empty($appointment->clinician) && !empty($appointment->clinician->user)) {
        $firstName = trim((string)($appointment->clinician->user->first_name ?? ''));
        $lastName = trim((string)($appointment->clinician->user->last_name ?? ''));
        $fullName = trim($firstName . ' ' . $lastName);

        if ($fullName !== '') {
            return $fullName;
        }
    }

    return 'Clinician #' . (int)$appointment->clinician_id;
};


// Build breadcrumbs based on user type
$breadcrumbs = [];

if ($isStaff) {
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
        ['title' => 'Appointments', 'url' => ['controller' => 'Appointments', 'action' => 'index']],
        ['title' => 'View', 'url' => null]
    ];
} else {
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'patient']],
        ['title' => 'Upcoming Appointments', 'url' => ['controller' => 'Appointments', 'action' => 'upcoming-appointments']],
        ['title' => 'View', 'url' => null]
    ];
}
?>

<!-- Header Section -->
<?= $this->element('page_header', [
    'breadcrumbs' => $breadcrumbs
]) ?>

<div class="form-container appointments-page appointments-detail-page">
    <div class="form-header appointments-page-header">
        <div class="appointments-page-heading">
            <h1><?= $isPatient ? __('Appointment Details') : __('Appointment Record') ?></h1>
            <p class="appointments-page-subtitle">
                <?php if ($isPatient): ?>
                    <?= __('Review your appointment information and contact the clinic if you need any changes.') ?>
                <?php else: ?>
                    <?= __('View the full appointment record and manage it if required.') ?>
                <?php endif; ?>
            </p>
        </div>

        <span class="appointment-status <?= h($statusClass($appointment->status ?? null)) ?>">
            <?= h($statusLabel($appointment->status ?? null)) ?>
        </span>
    </div>

    <?php if ($isPatient): ?>
        <div class="form-info-alert">
            <i class="bi bi-info-circle"></i>
            <p>
                <?= __('Patients can view their own appointment details here and contact the clinic if they need help with an appointment.') ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-clipboard2-check"></i>
            <?= __('Appointment Summary') ?>
        </h2>

        <div class="appointments-detail-grid<?= $isPatient ? ' appointments-detail-grid--three' : '' ?>">
            <?php if (!$isPatient): ?>
                <div class="appointments-detail-item">
                    <div class="appointments-detail-label"><?= __('Appointment ID') ?></div>
                    <div class="appointments-detail-value">#<?= h($appointment->id) ?></div>
                </div>
            <?php endif; ?>

            <div class="appointments-detail-item">
                <div class="appointments-detail-label"><?= __('Scheduled For') ?></div>
                <div class="appointments-detail-value"><?= h($formatDateTime($appointment->scheduled_at ?? null)) ?></div>
            </div>

            <div class="appointments-detail-item">
                <div class="appointments-detail-label"><?= __('Duration') ?></div>
                <div class="appointments-detail-value">
                    <?= !empty($appointment->duration) ? h($appointment->duration) . ' min' : __('Not provided') ?>
                </div>
            </div>

            <?php if (!$isPatient): ?>
                <div class="appointments-detail-item">
                    <div class="appointments-detail-label"><?= __('Status') ?></div>
                    <div class="appointments-detail-value"><?= h($statusLabel($appointment->status ?? null)) ?></div>
                </div>
            <?php endif; ?>

            <?php if ($isStaff): ?>
                <div class="appointments-detail-item">
                    <div class="appointments-detail-label"><?= __('Patient') ?></div>
                    <div class="appointments-detail-value"><?= h($getPatientName($appointment)) ?></div>
                </div>
            <?php endif; ?>

            <div class="appointments-detail-item">
                <div class="appointments-detail-label"><?= __('Clinician') ?></div>
                <div class="appointments-detail-value"><?= h($getClinicianName($appointment)) ?></div>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-lightning-charge"></i>
            <?= __('Actions') ?>
        </h2>

        <div class="appointments-action-row">
            <?php if ($isPatient): ?>
                <?= $this->Html->link(
                    '<i class="bi bi-envelope"></i><span>Contact Clinic About Appointment</span>',
                    ['controller' => 'ContactMessages', 'action' => 'add'],
                    ['class' => 'btn btn-primary', 'escape' => false]
                ) ?>
            <?php else: ?>
                <?= $this->Html->link(
                    '<i class="bi bi-pencil-square"></i><span>Edit Appointment</span>',
                    ['action' => 'edit', $appointment->id],
                    ['class' => 'btn btn-primary', 'escape' => false]
                ) ?>

                <?= $this->Form->postLink(
                    '<i class="bi bi-trash"></i><span>Delete Appointment</span>',
                    ['action' => 'delete', $appointment->id],
                    [
                        'confirm' => __('Are you sure you want to delete appointment # {0}?', $appointment->id),
                        'class' => 'btn btn-outline-danger',
                        'escape' => false,
                    ]
                ) ?>

                <?= $this->Html->link(
                    '<i class="bi bi-plus-circle"></i><span>New Appointment</span>',
                    ['action' => 'add'],
                    ['class' => 'btn btn-outline-primary', 'escape' => false]
                ) ?>
            <?php endif; ?>
        </div>
    </div>
</div>
