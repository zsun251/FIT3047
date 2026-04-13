<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Symptom $symptom
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Symptom'), ['action' => 'edit', $symptom->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Symptom'), ['action' => 'delete', $symptom->id], ['confirm' => __('Are you sure you want to delete # {0}?', $symptom->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Symptoms'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Symptom'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="symptoms view content">
            <h3><?= h($symptom->symptom_name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Symptom Name') ?></th>
                    <td><?= h($symptom->symptom_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($symptom->id) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Description') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($symptom->description)); ?>
                </blockquote>
            </div>
            <div class="related">
                <h4><?= __('Related Vital Data') ?></h4>
                <?php if (!empty($symptom->vital_data)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Patient Id') ?></th>
                            <th><?= __('Oxygen Saturation') ?></th>
                            <th><?= __('Respiratory Rate') ?></th>
                            <th><?= __('Lung Function') ?></th>
                            <th><?= __('Blood Pressure') ?></th>
                            <th><?= __('Pulse Oximetry') ?></th>
                            <th><?= __('Weight') ?></th>
                            <th><?= __('Recorded At') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($symptom->vital_data as $vitalData) : ?>
                        <tr>
                            <td><?= h($vitalData->id) ?></td>
                            <td><?= h($vitalData->patient_id) ?></td>
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
        </div>
    </div>
</div>