<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Symptom $symptom
 * @var string[]|\Cake\Collection\CollectionInterface $vitalData
 */

$this->assign('title', __('Edit Symptom'));
echo $this->Html->css('form-shared', ['block' => true]);
?>
<?= $this->element('page_header', ['backUrl' => ['action' => 'index']]) ?>

<div class="form-container">
    <div class="form-header">
        <h1><?= __('Edit Symptom') ?></h1>
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

    <?= $this->Form->create($symptom) ?>

    <div class="form-section">
        <div class="form-grid">
            <div class="form-group form-grid-full">
                <?= $this->Form->control('symptom_name') ?>
            </div>
            <div class="form-group form-grid-full">
                <?= $this->Form->control('description', ['class' => 'form-control']) ?>
            </div>
            <div class="form-group form-grid-full">
                <?= $this->Form->control('vital_data._ids', ['options' => $vitalData, 'class' => 'form-select']) ?>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> <?= __('Save Changes') ?>
        </button>
        <?= $this->Html->link(
            '<i class="bi bi-x-circle"></i> ' . __('Cancel'),
            ['action' => 'index'],
            ['class' => 'btn btn-outline-secondary', 'escape' => false]
        ) ?>
    </div>

    <?= $this->Form->end() ?>
</div>
