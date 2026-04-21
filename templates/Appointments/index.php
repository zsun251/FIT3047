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

$this->assign('title', 'Appointments');

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
?>

<div class="appointments index content">
    <h3>
        <?= $isAdmin ? __('Appointments') : __('Assigned Appointments') ?>
    </h3>

    <p>
        <?php if ($isAdmin): ?>
            <?= __('Manage appointment records across the system.') ?>
        <?php elseif ($isClinician): ?>
            <?= __('Review appointments relevant to your patients.') ?>
        <?php else: ?>
            <?= __('Appointment records') ?>
        <?php endif; ?>
    </p>

    <div class="table-responsive">
        <table>
            <thead>
            <tr>
                <th><?= $this->Paginator->sort('id', 'ID') ?></th>
                <th><?= $this->Paginator->sort('patient_id', 'Patient') ?></th>
                <th><?= $this->Paginator->sort('clinician_id', 'Clinician') ?></th>
                <th><?= $this->Paginator->sort('scheduled_at', 'Scheduled At') ?></th>
                <th><?= $this->Paginator->sort('duration', 'Duration') ?></th>
                <th><?= $this->Paginator->sort('status', 'Status') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($appointmentList as $appointment): ?>
                <tr>
                    <td><?= $this->Number->format($appointment->id) ?></td>
                    <td><?= h($getPatientLabel($appointment)) ?></td>
                    <td><?= h($getClinicianLabel($appointment)) ?></td>
                    <td><?= h($appointment->scheduled_at) ?></td>
                    <td><?= $this->Number->format($appointment->duration) ?></td>
                    <td><?= h($appointment->status) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $appointment->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $appointment->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $appointment->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $appointment->id),
                            ]
                        ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (empty($appointmentList)): ?>
        <div class="appointments-empty-state" style="text-align:center; margin-top: 2rem;">
            <p><?= __('No appointments found.') ?></p>
            <?= $this->Html->link(
                __('Create Appointment'),
                ['action' => 'add'],
                ['class' => 'button']
            ) ?>
        </div>
    <?php endif; ?>

    <div class="paginator">
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
</div>
