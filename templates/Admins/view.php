<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin $admin
 */

$this->assign('title', __('Admin Details'));
echo $this->Html->css('view-shared', ['block' => true]);
?>
<?= $this->element('page_header', ['backUrl' => ['action' => 'index']]) ?>

<div class="view-container">
    <div class="view-header">
        <h1><?= __('Admin') ?> #<?= h($admin->id) ?></h1>
        <div class="view-actions">
            <?= $this->Html->link(
                '<i class="bi bi-pencil"></i> ' . __('Edit'),
                ['action' => 'edit', $admin->id],
                ['class' => 'btn btn-primary', 'escape' => false]
            ) ?>
            <?= $this->Form->postLink(
                '<i class="bi bi-trash"></i> ' . __('Delete'),
                ['action' => 'delete', $admin->id],
                [
                    'confirm' => __('Are you sure you want to delete # {0}?', $admin->id),
                    'class' => 'btn btn-outline-danger',
                    'escape' => false,
                ]
            ) ?>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><?= __('User') ?></div>
            <div class="detail-value"><?= $admin->hasValue('user') ? $this->Html->link($admin->user->username, ['controller' => 'Users', 'action' => 'view', $admin->user->id]) : '' ?></div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><?= __('Id') ?></div>
            <div class="detail-value"><?= $this->Number->format($admin->id) ?></div>
        </div>
    </div>
</div>
