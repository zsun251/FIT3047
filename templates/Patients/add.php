<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Patient $patient
 * @var \Cake\Collection\CollectionInterface|string[] $users
 * @var \Cake\Collection\CollectionInterface|string[] $clinicians
 */

$this->assign('title', 'Add Patient');
echo $this->Html->css('form-shared', ['block' => true]);

$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
    ['title' => 'Patients',  'url' => ['controller' => 'Patients',  'action' => 'index']],
    ['title' => 'Add',       'url' => null],
];
?>

<?= $this->element('page_header', ['breadcrumbs' => $breadcrumbs]) ?>

<div class="form-container">
    <div class="form-header">
        <div>
            <h1><i class="bi bi-person-plus" style="color:#0d6efd;margin-right:.5rem;"></i><?= __('Add Patient') ?></h1>
            <small class="text-muted">Assign an existing user as a patient</small>
        </div>
    </div>

    <?= $this->Form->create($patient) ?>

    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-person"></i> <?= __('User Assignment') ?>
        </h2>
        <div class="form-grid">
            <div class="form-group">
                <?= $this->Form->control('user_id', [
                    'label'    => 'User <span class="text-danger">*</span>',
                    'escape'   => false,
                    'type'     => 'select',
                    'options'  => $users,
                    'empty'    => '-- Select User --',
                    'class'    => 'form-select',
                    'required' => true,
                ]) ?>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-person-badge"></i> <?= __('Clinician Assignment') ?>
        </h2>
        <div class="form-grid">
            <div class="form-group form-grid-full">
                <?= $this->Form->control('clinicians._ids', [
                    'label'   => 'Assigned Clinicians',
                    'options' => $clinicians,
                    'class'   => 'form-select',
                    'size'    => 8,
                ]) ?>
                <div class="form-text"><i class="bi bi-info-circle"></i> Hold Ctrl / Cmd to select multiple clinicians.</div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> <?= __('Create Patient') ?>
        </button>
        <?= $this->Html->link(
            '<i class="bi bi-x-circle"></i> Cancel',
            ['action' => 'index'],
            ['class' => 'btn btn-outline-secondary', 'escape' => false]
        ) ?>
    </div>

    <?= $this->Form->end() ?>
</div>
