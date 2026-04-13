<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\VitalData $vitalDataEntity
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Vital Data'), ['action' => 'edit', $vitalDataEntity->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Vital Data'), ['action' => 'delete', $vitalDataEntity->id], ['confirm' => __('Are you sure you want to delete # {0}?', $vitalDataEntity->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Vital Data'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Vital Data'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="vitalData view content">
            <h3><?= h($vitalDataEntity->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Patient') ?></th>
                    <td><?= $vitalDataEntity->hasValue('patient') ? $this->Html->link($vitalDataEntity->patient->id, ['controller' => 'Patients', 'action' => 'view', $vitalDataEntity->patient->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Blood Pressure') ?></th>
                    <td><?= h($vitalDataEntity->blood_pressure) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($vitalDataEntity->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Oxygen Saturation') ?></th>
                    <td><?= $vitalDataEntity->oxygen_saturation === null ? '' : $this->Number->format($vitalDataEntity->oxygen_saturation) ?></td>
                </tr>
                <tr>
                    <th><?= __('Respiratory Rate') ?></th>
                    <td><?= $vitalDataEntity->respiratory_rate === null ? '' : $this->Number->format($vitalDataEntity->respiratory_rate) ?></td>
                </tr>
                <tr>
                    <th><?= __('Lung Function') ?></th>
                    <td><?= $vitalDataEntity->lung_function === null ? '' : $this->Number->format($vitalDataEntity->lung_function) ?></td>
                </tr>
                <tr>
                    <th><?= __('Pulse Oximetry') ?></th>
                    <td><?= $vitalDataEntity->pulse_oximetry === null ? '' : $this->Number->format($vitalDataEntity->pulse_oximetry) ?></td>
                </tr>
                <tr>
                    <th><?= __('Weight') ?></th>
                    <td><?= $vitalDataEntity->weight === null ? '' : $this->Number->format($vitalDataEntity->weight) ?></td>
                </tr>
                <tr>
                    <th><?= __('Recorded At') ?></th>
                    <td><?= h($vitalDataEntity->recorded_at) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Symptoms') ?></h4>
                <?php if (!empty($vitalDataEntity->symptoms)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Symptom Name') ?></th>
                            <th><?= __('Description') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($vitalDataEntity->symptoms as $symptom) : ?>
                        <tr>
                            <td><?= h($symptom->id) ?></td>
                            <td><?= h($symptom->symptom_name) ?></td>
                            <td><?= h($symptom->description) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Symptoms', 'action' => 'view', $symptom->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Symptoms', 'action' => 'edit', $symptom->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'Symptoms', 'action' => 'delete', $symptom->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $symptom->id),
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