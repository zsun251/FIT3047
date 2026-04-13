<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Patient $patient
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Patient'), ['action' => 'edit', $patient->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Patient'), ['action' => 'delete', $patient->id], ['confirm' => __('Are you sure you want to delete # {0}?', $patient->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Patients'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Patient'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="patients view content">
            <h3><?= h($patient->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $patient->hasValue('user') ? $this->Html->link($patient->user->username, ['controller' => 'Users', 'action' => 'view', $patient->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($patient->id) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Clinicians') ?></h4>
                <?php if (!empty($patient->clinicians)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($patient->clinicians as $clinician) : ?>
                        <tr>
                            <td><?= h($clinician->id) ?></td>
                            <td><?= h($clinician->user_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Clinicians', 'action' => 'view', $clinician->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Clinicians', 'action' => 'edit', $clinician->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'Clinicians', 'action' => 'delete', $clinician->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $clinician->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Appointments') ?></h4>
                <?php if (!empty($patient->appointments)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Clinician Id') ?></th>
                            <th><?= __('Scheduled At') ?></th>
                            <th><?= __('Duration') ?></th>
                            <th><?= __('Status') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($patient->appointments as $appointment) : ?>
                        <tr>
                            <td><?= h($appointment->id) ?></td>
                            <td><?= h($appointment->clinician_id) ?></td>
                            <td><?= h($appointment->scheduled_at) ?></td>
                            <td><?= h($appointment->duration) ?></td>
                            <td><?= h($appointment->status) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Appointments', 'action' => 'view', $appointment->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Appointments', 'action' => 'edit', $appointment->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'Appointments', 'action' => 'delete', $appointment->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $appointment->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Vital Data') ?></h4>
                <?php if (!empty($patient->vital_data)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Oxygen Saturation') ?></th>
                            <th><?= __('Respiratory Rate') ?></th>
                            <th><?= __('Lung Function') ?></th>
                            <th><?= __('Blood Pressure') ?></th>
                            <th><?= __('Pulse Oximetry') ?></th>
                            <th><?= __('Weight') ?></th>
                            <th><?= __('Recorded At') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($patient->vital_data as $vitalData) : ?>
                        <tr>
                            <td><?= h($vitalData->id) ?></td>
                            <td><?= h($vitalData->oxygen_saturation) ?></td>
                            <td><?= h($vitalData->respiratory_rate) ?></td>
                            <td><?= h($vitalData->lung_function) ?></td>
                            <td><?= h($vitalData->blood_pressure) ?></td>
                            <td><?= h($vitalData->pulse_oximetry) ?></td>
                            <td><?= h($vitalData->weight) ?></td>
                            <td><?= h($vitalData->recorded_at) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'VitalData', 'action' => 'view', $vitalData->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'VitalData', 'action' => 'edit', $vitalData->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'VitalData', 'action' => 'delete', $vitalData->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $vitalData->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Vitals Reminders') ?></h4>
                <?php if (!empty($patient->vitals_reminders)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Reminder Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($patient->vitals_reminders as $vitalsReminder) : ?>
                        <tr>
                            <td><?= h($vitalsReminder->id) ?></td>
                            <td><?= h($vitalsReminder->reminder_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'VitalsReminders', 'action' => 'view', $vitalsReminder->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'VitalsReminders', 'action' => 'edit', $vitalsReminder->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'VitalsReminders', 'action' => 'delete', $vitalsReminder->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $vitalsReminder->id),
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