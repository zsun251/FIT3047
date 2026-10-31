<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Admin> $admins
 */

$this->assign('title', __('Admins'));
?>
<?= $this->element('page_header', [
    'title' => __('Admins'),
    'subtitle' => __('Manage administrator accounts'),
    'backUrl' => ['controller' => 'Dashboard', 'action' => 'staff'],
    'action' => $this->Html->link(
        '<i class="bi bi-plus-circle"></i><span>' . __('New Admin') . '</span>',
        ['action' => 'add'],
        ['class' => 'rc-action-pill', 'escape' => false]
    ),
]) ?>

<div class="rc-card">
    <div class="table-responsive">
        <table class="rc-table">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $row): ?>
                <tr>
                    <td><?= $this->Number->format($row->id) ?></td>
                    <td><?= $row->hasValue('user') ? $this->Html->link($row->user->username, ['controller' => 'Users', 'action' => 'view', $row->user->id]) : '' ?></td>
                    <td class="actions"><?= $this->element('row_actions', ['id' => $row->id]) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= $this->element('table_pagination') ?>
</div>
