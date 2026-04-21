<?php
declare(strict_types=1);

use App\Policy\UserAccess;

/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Appointment> $appointments
 */

$identity = $this->request->getAttribute('identity');
$isAdmin = $identity && UserAccess::isAdmin($identity);
$isClinician = $identity && UserAccess::isClinician($identity);

$this->assign('title', $isAdmin ? 'Appointments' : 'Assigned Appointments');

echo $this->Html->css([
    'form-shared',
    'appointments',
], ['block' => true]);

$appointmentList = is_array($appointments) ? $appointments : iterator_to_array($appointments);

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

$formatDateTime = function ($dateTime): string {
    if (!$dateTime) {
        return 'Not scheduled';
    }

    return $dateTime->format('d M Y, g:i A');
};

$statusClass = function (?string $status): string {
    $value = strtolower(trim((string)$status));

    return match ($value) {
        'scheduled' => 'appointment-status-scheduled',
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
?>

<?= $this->Html->link(
    '<i class="bi bi-arrow-left"></i> Back to Dashboard',
    ['controller' => 'Dashboard', 'action' => 'staff'],
    ['class' => 'back-link', 'escape' => false]
) ?>

<div class="form-container appointments-page">
    <div class="form-header appointments-page-header">
        <div class="appointments-page-heading">
            <h1><?= $isAdmin ? __('Appointments') : __('Assigned Appointments') ?></h1>
            <p class="appointments-page-subtitle">
                <?php if ($isAdmin): ?>
                    <?= __('Manage appointment records across the system.') ?>
                <?php elseif ($isClinician): ?>
                    <?= __('Review and manage appointments related to your patients.') ?>
                <?php else: ?>
                    <?= __('Review appointment records.') ?>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <div class="form-info-alert">
        <i class="bi bi-info-circle"></i>
        <p>
            <?php if ($isAdmin): ?>
                <?= __('This appointments table keeps the staff view focused and easy to scan while preserving quick actions for each record.') ?>
            <?php else: ?>
                <?= __('This appointments table is designed to keep your assigned appointment records clear, readable, and quick to manage.') ?>
            <?php endif; ?>
        </p>
    </div>

    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-calendar2-week"></i>
            <?= __('Appointment Records') ?>
        </h2>

        <?php if (!empty($appointmentList)): ?>
            <div class="appointments-table-shell">
                <div class="table-responsive">
                    <table class="table appointments-table align-middle mb-0">
                        <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('id', 'ID') ?></th>
                            <th><?= $this->Paginator->sort('patient_id', 'Patient') ?></th>
                            <th><?= $this->Paginator->sort('clinician_id', 'Clinician') ?></th>
                            <th><?= $this->Paginator->sort('scheduled_at', 'Scheduled At') ?></th>
                            <th><?= $this->Paginator->sort('duration', 'Duration') ?></th>
                            <th><?= $this->Paginator->sort('status', 'Status') ?></th>
                            <th class="text-end"><?= __('Actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($appointmentList as $appointment): ?>
                            <tr>
                                <td>#<?= h($appointment->id) ?></td>
                                <td><?= h($getPatientLabel($appointment)) ?></td>
                                <td><?= h($getClinicianLabel($appointment)) ?></td>
                                <td><?= h($formatDateTime($appointment->scheduled_at ?? null)) ?></td>
                                <td>
                                    <?= !empty($appointment->duration) ? h($appointment->duration) . ' min' : __('Not provided') ?>
                                </td>
                                <td>
                                        <span class="appointment-status <?= h($statusClass($appointment->status ?? null)) ?>">
                                            <?= h($statusLabel($appointment->status ?? null)) ?>
                                        </span>
                                </td>
                                <td class="text-end">
                                    <div class="appointments-table-actions">
                                        <?= $this->Html->link(
                                            __('View'),
                                            ['action' => 'view', $appointment->id],
                                            ['class' => 'appointment-action appointment-action-view']
                                        ) ?>

                                        <?= $this->Html->link(
                                            __('Edit'),
                                            ['action' => 'edit', $appointment->id],
                                            ['class' => 'appointment-action appointment-action-edit']
                                        ) ?>

                                        <?= $this->Form->postLink(
                                            __('Delete'),
                                            ['action' => 'delete', $appointment->id],
                                            [
                                                'method' => 'delete',
                                                'confirm' => __('Are you sure you want to delete appointment # {0}?', $appointment->id),
                                                'class' => 'appointment-action appointment-action-delete',
                                            ]
                                        ) ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="appointments-pagination paginator">
                <ul class="pagination">
                    <?= $this->Paginator->first('<< ' . __('first')) ?>
                    <?= $this->Paginator->prev('< ' . __('previous')) ?>
                    <?= $this->Paginator->numbers() ?>
                    <?= $this->Paginator->next(__('next') . ' >') ?>
                    <?= $this->Paginator->last(__('last') . ' >>') ?>
                </ul>
                <p>
                    <?= $this->Paginator->counter(
                        __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')
                    ) ?>
                </p>
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

        <div class="appointments-bottom-action">
            <?= $this->Html->link(
                '<i class="bi bi-plus-circle"></i><span>Create Appointment</span>',
                ['action' => 'add'],
                ['class' => 'btn btn-primary', 'escape' => false]
            ) ?>
        </div>
    </div>
</div>
