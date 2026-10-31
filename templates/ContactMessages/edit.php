<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ContactMessage $contactMessage
 */

$this->assign('title', __('Edit Contact Message'));
echo $this->Html->css('form-shared', ['block' => true]);
?>
<?= $this->element('page_header', ['backUrl' => ['action' => 'index']]) ?>

<div class="form-container">
    <div class="form-header">
        <h1><?= __('Edit Contact Message') ?></h1>
        <?= $this->Form->postLink(
            '<i class="bi bi-trash"></i> ' . __('Delete'),
            ['action' => 'delete', $contactMessage->id],
            [
                'confirm' => __('Are you sure you want to delete # {0}?', $contactMessage->id),
                'class' => 'btn btn-outline-danger',
                'escape' => false,
            ]
        ) ?>
    </div>

    <?= $this->Form->create($contactMessage) ?>

    <div class="form-section">
        <div class="form-grid">
            <div class="form-group">
                <?= $this->Form->control('first_name') ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('last_name') ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('contact_number') ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('email') ?>
            </div>
            <div class="form-group form-grid-full">
                <?= $this->Form->control('message', ['class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('status') ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('submitted_at', ['class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('reviewed_by') ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('reviewed_at', ['empty' => true, 'class' => 'form-control']) ?>
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
