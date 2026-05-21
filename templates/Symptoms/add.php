<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Symptom $symptom
 * @var \Cake\Collection\CollectionInterface|string[] $vitalData
 */

$this->assign('title', 'Add Symptom');
echo $this->Html->css('form-shared', ['block' => true]);

$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
    ['title' => 'Symptoms',  'url' => ['controller' => 'Symptoms',  'action' => 'index']],
    ['title' => 'Add',       'url' => null],
];
?>

<?= $this->element('page_header', ['breadcrumbs' => $breadcrumbs]) ?>

<div class="form-container">
    <div class="form-header">
        <div>
            <h1><i class="bi bi-plus-circle" style="color:#0d6efd;margin-right:.5rem;"></i><?= __('Add Symptom') ?></h1>
            <small class="text-muted">Create a new symptom record</small>
        </div>
    </div>

    <?= $this->Form->create($symptom) ?>

    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-card-text"></i> <?= __('Symptom Details') ?>
        </h2>
        <div class="form-grid">
            <div class="form-group form-grid-full">
                <?= $this->Form->control('symptom_name', [
                    'label'       => 'Symptom Name <span class="text-danger">*</span>',
                    'escape'      => false,
                    'required'    => true,
                    'class'       => 'form-control',
                    'placeholder' => 'e.g. Shortness of breath',
                ]) ?>
            </div>
            <div class="form-group form-grid-full">
                <?= $this->Form->control('description', [
                    'label'       => 'Description',
                    'type'        => 'textarea',
                    'rows'        => 4,
                    'class'       => 'form-control',
                    'placeholder' => 'Describe the symptom in detail…',
                ]) ?>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-heart-pulse"></i> <?= __('Related Vital Data') ?>
        </h2>
        <div class="form-grid">
            <div class="form-group form-grid-full">
                <?= $this->Form->control('vital_data._ids', [
                    'label'   => 'Linked Vital Records',
                    'options' => $vitalData,
                    'class'   => 'form-select',
                    'size'    => 8,
                ]) ?>
                <div class="form-text"><i class="bi bi-info-circle"></i> Hold Ctrl / Cmd to select multiple records.</div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> <?= __('Create Symptom') ?>
        </button>
        <?= $this->Html->link(
            '<i class="bi bi-x-circle"></i> Cancel',
            ['action' => 'index'],
            ['class' => 'btn btn-outline-secondary', 'escape' => false]
        ) ?>
    </div>

    <?= $this->Form->end() ?>
</div>
