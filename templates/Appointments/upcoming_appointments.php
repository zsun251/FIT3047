<?php
declare(strict_types=1);

/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Appointment> $appointments
 */

use App\Policy\UserAccess;

$this->assign('title', 'My Appointments');

echo $this->Html->css(['form-shared', 'appointments',], ['block' => true]);

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

$identity = $this->request->getAttribute('identity');
$isStaff = UserAccess::isStaff($identity);

// Build breadcrumbs based on user type
$breadcrumbs = [];

if ($isStaff) {
    $breadcrumbs = [
    ];
} else {
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'patient']],
        ['title' => 'Upcoming Appointments', 'url' => null]
    ];
}
?>

<!-- Header Section -->
<?= $this->element('page_header', [
    'breadcrumbs' => $breadcrumbs
]) ?>

<div class="form-container appointments-page">
    <div class="form-header appointments-page-header">
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
            <div class="appointments-card-list">
                <?php foreach ($upcomingAppointments as $appointment): ?>
                    <article class="appointment-card">
                        <div class="appointment-card-header">
                            <div>
                                <div class="appointment-card-title">
                                    <?= h($formatDate($appointment->scheduled_at ?? null)) ?>
                                </div>
                                <div class="appointment-card-subtitle">
                                    <?= h($formatTime($appointment->scheduled_at ?? null)) ?>
                                </div>

                                <div class="appointment-card-meta">
                                    <span class="appointment-meta-pill">
                                        <i class="bi bi-hourglass-split"></i>
                                        <?= !empty($appointment->duration) ? h($appointment->duration) . ' min' : __('Duration not provided') ?>
                                    </span>
                                </div>
                            </div>

                            <span class="appointment-status <?= h($statusClass($appointment->status ?? null)) ?>">
                                <?= h($statusLabel($appointment->status ?? null)) ?>
                            </span>
                        </div>

                        <div class="appointments-detail-grid appointments-detail-grid--compact">
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

                        <div class="appointment-card-actions">
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

            <div class="appointments-card-list appointments-card-list--history">
                <?php foreach ($pastAppointments as $appointment): ?>
                    <article class="appointment-card appointment-card--muted">
                        <div class="appointment-card-header">
                            <div>
                                <div class="appointment-card-title">
                                    <?= h($formatDate($appointment->scheduled_at ?? null)) ?>
                                </div>
                                <div class="appointment-card-subtitle">
                                    <?= h($formatTime($appointment->scheduled_at ?? null)) ?>
                                </div>

                                <div class="appointment-card-meta">
                                    <span class="appointment-meta-pill">
                                        <i class="bi bi-hourglass-split"></i>
                                        <?= !empty($appointment->duration) ? h($appointment->duration) . ' min' : __('Duration not provided') ?>
                                    </span>
                                </div>
                            </div>

                            <span class="appointment-status <?= h($statusClass($appointment->status ?? null)) ?>">
                                <?= h($statusLabel($appointment->status ?? null)) ?>
                            </span>
                        </div>

                        <div class="appointments-detail-grid appointments-detail-grid--compact">
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

                        <div class="appointment-card-actions">
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
