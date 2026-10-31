<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Symptom $symptom
 */

$this->assign('title', __('Symptom Details'));
echo $this->Html->css('view-shared', ['block' => true]);
?>
<?= $this->element('page_header', ['backUrl' => ['action' => 'index']]) ?>

<div class="view-container">
    <div class="view-header">
        <h1><?= h($symptom->symptom_name) ?></h1>
        <div class="view-actions">
            <?= $this->Html->link(
                '<i class="bi bi-pencil"></i> ' . __('Edit'),
                ['action' => 'edit', $symptom->id],
                ['class' => 'btn btn-primary', 'escape' => false]
            ) ?>
            <?= $this->Form->postLink(
                '<i class="bi bi-trash"></i> ' . __('Delete'),
                ['action' => 'delete', $symptom->id],
                [
                    'confirm' => __('Are you sure you want to delete # {0}?', $symptom->id),
                    'class' => 'btn btn-outline-danger',
                    'escape' => false,
                ]
            ) ?>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><?= __('Symptom Name') ?></div>
            <div class="detail-value"><?= h($symptom->symptom_name) ?></div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><?= __('Id') ?></div>
            <div class="detail-value"><?= $this->Number->format($symptom->id) ?></div>
        </div>
        <div class="detail-item detail-full">
            <div class="detail-label"><?= __('Description') ?></div>
            <div class="detail-value"><?= $this->Text->autoParagraph(h($symptom->description)); ?></div>
        </div>
    </div>

    <div class="related-section">
        <h2><?= __('Related Vital Data') ?></h2>
        <?php if (!empty($symptom->vital_data)) : ?>
        <div class="table-responsive">
            <table class="rc-table">
                <thead>
                    <tr>
                        <th><?= __('Id') ?></th>
                        <th><?= __('Patient Id') ?></th>
                        <th><?= __('Oxygen Saturation') ?></th>
                        <th><?= __('Respiratory Rate') ?></th>
                        <th><?= __('Blood Pressure') ?></th>
                        <th><?= __('Pulse Oximetry') ?></th>
                        <th><?= __('Weight') ?></th>
                        <th><?= __('Recorded At') ?></th>
                        <th class="actions"><?= __('Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($symptom->vital_data as $vitalData) : ?>
                    <tr>
                        <td><?= h($vitalData->id) ?></td>
                        <td><?= h($vitalData->patient_id) ?></td>
                        <td><?= h($vitalData->oxygen_saturation) ?></td>
                        <td><?= h($vitalData->respiratory_rate) ?></td>
                        <td><?= h($vitalData->blood_pressure) ?></td>
                        <td><?= h($vitalData->pulse_oximetry) ?></td>
                        <td><?= h($vitalData->weight) ?></td>
                        <td><?= h($vitalData->recorded_at) ?></td>
                        <td class="actions"><?= $this->element('row_actions', ['controller' => 'VitalData', 'id' => $vitalData->id]) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else : ?>
        <p class="related-empty"><?= __('Nothing to show yet.') ?></p>
        <?php endif; ?>
    </div>
</div>
