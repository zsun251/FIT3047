<?php
declare(strict_types=1);

/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Appointment> $appointments
 */

$this->assign('title', 'My Appointments');

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

$isPastAppointment = function ($appointment): bool {
    if (empty($appointment->scheduled_at)) {
        return false;
    }

    return $appointment->scheduled_at->getTimestamp() < time();
};

$formatDateTime = function ($dateTime): string {
    if (!$dateTime) {
        return 'Not scheduled';
    }

    return $dateTime->format('d M Y, g:i A');
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

<div class="appointments index content">
    <h3><?= __('My Appointments') ?></h3>
    <p><?= __('View your upcoming appointments and appointment history.') ?></p>

    <h4><?= __('Upcoming Appointments') ?></h4>

    <?php if (!empty($upcomingAppointments)): ?>
        <div class="table-responsive">
            <table>
                <thead>
                <tr>
                    <th><?= __('Date & Time') ?></th>
                    <th><?= __('Clinician') ?></th>
                    <th><?= __('Duration') ?></th>
                    <th><?= __('Status') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($upcomingAppointments as $appointment): ?>
                    <tr>
                        <td><?= h($formatDateTime($appointment->scheduled_at ?? null)) ?></td>
                        <td><?= h($getClinicianLabel($appointment)) ?></td>
                        <td>
                            <?= !empty($appointment->duration) ? h($appointment->duration) . ' min' : __('Not provided') ?>
                        </td>
                        <td><?= h($appointment->status) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['action' => 'view', $appointment->id]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p><?= __('You do not currently have any upcoming appointments.') ?></p>
    <?php endif; ?>

    <?php if (!empty($pastAppointments)): ?>
        <h4 style="margin-top: 2rem;"><?= __('Appointment History') ?></h4>

        <div class="table-responsive">
            <table>
                <thead>
                <tr>
                    <th><?= __('Date & Time') ?></th>
                    <th><?= __('Clinician') ?></th>
                    <th><?= __('Duration') ?></th>
                    <th><?= __('Status') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pastAppointments as $appointment): ?>
                    <tr>
                        <td><?= h($formatDateTime($appointment->scheduled_at ?? null)) ?></td>
                        <td><?= h($getClinicianLabel($appointment)) ?></td>
                        <td>
                            <?= !empty($appointment->duration) ? h($appointment->duration) . ' min' : __('Not provided') ?>
                        </td>
                        <td><?= h($appointment->status) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['action' => 'view', $appointment->id]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if (empty($appointmentList)): ?>
        <div class="appointments-empty-state" style="text-align:center; margin-top: 2rem;">
            <p><?= __('No appointments found.') ?></p>
            <?= $this->Html->link(
                __('Contact Clinic About Appointments'),
                ['controller' => 'ContactMessages', 'action' => 'add'],
                ['class' => 'button']
            ) ?>
        </div>
    <?php endif; ?>
</div>
