<?php
declare(strict_types=1);

/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Appointment> $appointments
 */

$this->assign('title', 'My Appointments');

echo $this->Html->css([
    'form-shared',
    'appointments',
    'appointment-patient',
], ['block' => true]);

$appointmentList = is_array($appointments) ? $appointments : iterator_to_array($appointments);

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

    return 'Assigned clinician';
};

$statusClass = function (?string $status): string {
    $value = strtolower(trim((string)$status));

    return match ($value) {
        'scheduled', 'upcoming' => 'appointment-status-scheduled',
        'confirmed' => 'appointment-status-confirmed',
        'completed' => 'appointment-status-completed',
        'cancelled', 'canceled' => 'appointment-status-cancelled',
        'pending' => 'appointment-status-pending',
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

$formatDate = function ($dateTime): string {
    if (!$dateTime) {
        return 'Not scheduled';
    }

    return $dateTime->format('l, d M Y');
};

$formatTime = function ($dateTime): string {
    if (!$dateTime) {
        return 'Time unavailable';
    }

    return $dateTime->format('g:i A');
};

$formatDateTime = function ($dateTime): string {
    if (!$dateTime) {
        return 'Not scheduled';
    }

    return $dateTime->format('d M Y, g:i A');
};

$isPastAppointment = function ($appointment): bool {
    $status = strtolower(trim((string)($appointment->status ?? '')));

    if (in_array($status, ['completed', 'cancelled', 'canceled'], true)) {
        return true;
    }

    if (empty($appointment->scheduled_at)) {
        return false;
    }

    return $appointment->scheduled_at->getTimestamp() < time();
};

$upcomingAppointments = [];
$pastAppointments = [];

foreach ($appointmentList as $appointment) {
    if ($isPastAppointment($appointment)) {
        $pastAppointments[] = $appointment;
    } else {
        $upcomingAppointments[] = $appointment;
    }
}
?>

<?= $this->Html->link(
    '<i class="bi bi-arrow-left"></i> Back to Dashboard',
    ['controller' => 'Dashboard', 'action' => 'patient'],
    ['class' => 'back-link', 'escape' => false]
) ?>

<div class="form-container patient-appointments-page">
    <div class="form-header appointments-page-header patient-appointments-header">
        <div class="appointments-page-heading">
            <h1><?= __('My Appointments') ?></h1>
            <p class="appointments-page-subtitle">
                <?= __('Review your upcoming appointments and your recent appointment history in one place.') ?>
            </p>
        </div>
    </div>

    <div class="form-info-alert">
        <i class="bi bi-info-circle"></i>
        <p>
            <?= __('If you need help with an appointment, use the contact button below to send a message to the clinic.') ?>
        </p>
    </div>

    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-calendar2-event"></i>
            <?= __('Upcoming Appointments') ?>
        </h2>

        <?php if (!empty($upcomingAppointments)): ?>
            <div class="patient-appointments-list">
                <?php foreach ($upcomingAppointments as $appointment): ?>
                    <article class="patient-appointment-card">
                        <div class="patient-appointment-top">
                            <div>
                                <div class="patient-appointment-date">
                                    <?= h($formatDate($appointment->scheduled_at ?? null)) ?>
                                </div>
                                <div class="patient-appointment-time">
                                    <?= h($formatTime($appointment->scheduled_at ?? null)) ?>
                                </div>

                                <div class="patient-appointment-meta-row">
                                    <span class="patient-appointment-meta-pill">
                                        <i class="bi bi-hourglass-split"></i>
                                        <?= !empty($appointment->duration) ? h($appointment->duration) . ' min' : __('Duration not provided') ?>
                                    </span>
                                </div>
                            </div>

                            <span class="appointment-status <?= h($statusClass($appointment->status ?? null)) ?>">
                                <?= h($statusLabel($appointment->status ?? null)) ?>
                            </span>
                        </div>

                        <div class="appointments-detail-grid patient-appointment-detail-grid patient-appointment-detail-grid-compact">
                            <div class="appointments-detail-item">
                                <div class="appointments-detail-label"><?= __('Date & Time') ?></div>
                                <div class="appointments-detail-value">
                                    <?= h($formatDateTime($appointment->scheduled_at ?? null)) ?>
                                </div>
                            </div>

                            <div class="appointments-detail-item">
                                <div class="appointments-detail-label"><?= __('Clinician') ?></div>
                                <div class="appointments-detail-value">
                                    <?= h($getClinicianLabel($appointment)) ?>
                                </div>
                            </div>
                        </div>

                        <div class="patient-appointment-actions">
                            <?= $this->Html->link(
                                '<i class="bi bi-eye"></i><span>View Details</span>',
                                ['action' => 'view', $appointment->id],
                                ['class' => 'btn btn-outline-primary', 'escape' => false]
                            ) ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="appointments-empty-state">
                <div class="appointments-empty-icon">
                    <i class="bi bi-calendar-x"></i>
                </div>
                <h3><?= __('No upcoming appointments') ?></h3>
                <p><?= __('You do not currently have any upcoming appointments scheduled.') ?></p>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($pastAppointments)): ?>
        <div class="form-section">
            <h2 class="form-section-title">
                <i class="bi bi-clock-history"></i>
                <?= __('Appointment History') ?>
            </h2>

            <div class="patient-appointments-list patient-appointments-history-list">
                <?php foreach ($pastAppointments as $appointment): ?>
                    <article class="patient-appointment-card patient-appointment-card-history">
                        <div class="patient-appointment-top">
                            <div>
                                <div class="patient-appointment-date">
                                    <?= h($formatDate($appointment->scheduled_at ?? null)) ?>
                                </div>
                                <div class="patient-appointment-time">
                                    <?= h($formatTime($appointment->scheduled_at ?? null)) ?>
                                </div>

                                <div class="patient-appointment-meta-row">
                                    <span class="patient-appointment-meta-pill">
                                        <i class="bi bi-hourglass-split"></i>
                                        <?= !empty($appointment->duration) ? h($appointment->duration) . ' min' : __('Duration not provided') ?>
                                    </span>
                                </div>
                            </div>

                            <span class="appointment-status <?= h($statusClass($appointment->status ?? null)) ?>">
                                <?= h($statusLabel($appointment->status ?? null)) ?>
                            </span>
                        </div>

                        <div class="appointments-detail-grid patient-appointment-detail-grid patient-appointment-detail-grid-compact">
                            <div class="appointments-detail-item">
                                <div class="appointments-detail-label"><?= __('Date & Time') ?></div>
                                <div class="appointments-detail-value">
                                    <?= h($formatDateTime($appointment->scheduled_at ?? null)) ?>
                                </div>
                            </div>

                            <div class="appointments-detail-item">
                                <div class="appointments-detail-label"><?= __('Clinician') ?></div>
                                <div class="appointments-detail-value">
                                    <?= h($getClinicianLabel($appointment)) ?>
                                </div>
                            </div>
                        </div>

                        <div class="patient-appointment-actions">
                            <?= $this->Html->link(
                                '<i class="bi bi-eye"></i><span>View Details</span>',
                                ['action' => 'view', $appointment->id],
                                ['class' => 'btn btn-outline-primary', 'escape' => false]
                            ) ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="appointments-bottom-action">
        <?= $this->Html->link(
            '<i class="bi bi-envelope"></i><span>Contact Clinic About Appointments</span>',
            ['controller' => 'ContactMessages', 'action' => 'add'],
            ['class' => 'btn btn-primary', 'escape' => false]
        ) ?>
    </div>

    <?php if (!empty($appointmentList)): ?>
        <div class="appointments-pagination paginator">
            <ul class="pagination">
                <?= $this->Paginator->prev('< ' . __('previous')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__('next') . ' >') ?>
            </ul>
            <p>
                <?= $this->Paginator->counter(
                    __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')
                ) ?>
            </p>
        </div>
    <?php endif; ?>
</div>
