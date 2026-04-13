<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Appointment $appointment
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Appointment'), ['action' => 'edit', $appointment->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Appointment'), ['action' => 'delete', $appointment->id], ['confirm' => __('Are you sure you want to delete # {0}?', $appointment->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Appointments'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Appointment'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="appointments view content">
            <h3><?= h($appointment->status) ?></h3>
            <table>
                <tr>
                    <th><?= __('Patient') ?></th>
                    <td><?= $appointment->hasValue('patient') ? $this->Html->link($appointment->patient->id, ['controller' => 'Patients', 'action' => 'view', $appointment->patient->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Clinician') ?></th>
                    <td><?= $appointment->hasValue('clinician') ? $this->Html->link($appointment->clinician->id, ['controller' => 'Clinicians', 'action' => 'view', $appointment->clinician->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= h($appointment->status) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($appointment->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Duration') ?></th>
                    <td><?= $this->Number->format($appointment->duration) ?></td>
                </tr>
                <tr>
                    <th><?= __('Scheduled At') ?></th>
                    <td><?= h($appointment->scheduled_at) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Appointment Reminders') ?></h4>
                <?php if (!empty($appointment->appointment_reminders)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Reminder Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($appointment->appointment_reminders as $appointmentReminder) : ?>
                        <tr>
                            <td><?= h($appointmentReminder->id) ?></td>
                            <td><?= h($appointmentReminder->reminder_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'AppointmentReminders', 'action' => 'view', $appointmentReminder->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'AppointmentReminders', 'action' => 'edit', $appointmentReminder->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'AppointmentReminders', 'action' => 'delete', $appointmentReminder->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $appointmentReminder->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>