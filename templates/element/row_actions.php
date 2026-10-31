<?php
/**
 * View, edit and delete icon buttons for a table row.
 *
 * @var \App\View\AppView $this
 * @var int $id Record id
 * @var string|null $controller Controller the actions belong to (defaults to the current one)
 */
$controller = $controller ?? $this->request->getParam('controller');
?>
<div class="rc-row-actions">
    <?= $this->Html->link(
        '<i class="bi bi-eye"></i>',
        ['controller' => $controller, 'action' => 'view', $id],
        ['class' => 'rc-icon-action', 'escape' => false, 'title' => __('View'), 'aria-label' => __('View')]
    ) ?>
    <?= $this->Html->link(
        '<i class="bi bi-pencil"></i>',
        ['controller' => $controller, 'action' => 'edit', $id],
        ['class' => 'rc-icon-action', 'escape' => false, 'title' => __('Edit'), 'aria-label' => __('Edit')]
    ) ?>
    <?= $this->Form->postLink(
        '<i class="bi bi-trash"></i>',
        ['controller' => $controller, 'action' => 'delete', $id],
        [
            'method' => 'delete',
            'confirm' => __('Are you sure you want to delete # {0}?', $id),
            'class' => 'rc-icon-action rc-icon-action-danger',
            'escape' => false,
            'title' => __('Delete'),
            'aria-label' => __('Delete'),
        ]
    ) ?>
</div>
