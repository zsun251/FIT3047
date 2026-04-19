<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\VitalData> $vitalData
 */
?>
<div class="vitalData index content">
    <?= $this->Html->link(__('New Vital Data'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Vital Data') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('patient_id') ?></th>
                    <th><?= $this->Paginator->sort('oxygen_saturation') ?></th>
                    <th><?= $this->Paginator->sort('respiratory_rate') ?></th>
                    <th><?= $this->Paginator->sort('lung_function') ?></th>
                    <th><?= $this->Paginator->sort('blood_pressure') ?></th>
                    <th><?= $this->Paginator->sort('pulse_oximetry') ?></th>
                    <th><?= $this->Paginator->sort('weight') ?></th>
                    <th><?= $this->Paginator->sort('recorded_at') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vitalData as $vitalDataEntity): ?>
                <tr>
                    <td><?= $this->Number->format($vitalDataEntity->id) ?></td>
                    <td><?= $vitalDataEntity->hasValue('patient') ? $this->Html->link($vitalDataEntity->patient->id, ['controller' => 'Patients', 'action' => 'view', $vitalDataEntity->patient->id]) : '' ?></td>
                    <td><?= $vitalDataEntity->oxygen_saturation === null ? '' : $this->Number->format($vitalDataEntity->oxygen_saturation) ?></td>
                    <td><?= $vitalDataEntity->respiratory_rate === null ? '' : $this->Number->format($vitalDataEntity->respiratory_rate) ?></td>
                    <td><?= $vitalDataEntity->lung_function === null ? '' : $this->Number->format($vitalDataEntity->lung_function) ?></td>
                    <td><?= h($vitalDataEntity->blood_pressure) ?></td>
                    <td><?= $vitalDataEntity->pulse_oximetry === null ? '' : $this->Number->format($vitalDataEntity->pulse_oximetry) ?></td>
                    <td><?= $vitalDataEntity->weight === null ? '' : $this->Number->format($vitalDataEntity->weight) ?></td>
                    <td><?= h($vitalDataEntity->recorded_at) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $vitalDataEntity->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $vitalDataEntity->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $vitalDataEntity->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $vitalDataEntity->id),
                            ]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
