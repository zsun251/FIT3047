<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\ContactMessage> $contactMessages
 */

$this->assign('title', __('Contact Us'));
?>
<?= $this->element('page_header', [
    'title' => __('Contact Messages'),
    'subtitle' => __('Messages sent through the contact form'),
    'backUrl' => ['controller' => 'Dashboard', 'action' => 'staff'],
    'action' => $this->Html->link(
        '<i class="bi bi-plus-circle"></i><span>' . __('New Contact Message') . '</span>',
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
                    <th><?= $this->Paginator->sort('first_name') ?></th>
                    <th><?= $this->Paginator->sort('last_name') ?></th>
                    <th><?= $this->Paginator->sort('contact_number') ?></th>
                    <th><?= $this->Paginator->sort('email') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <th><?= $this->Paginator->sort('submitted_at') ?></th>
                    <th><?= $this->Paginator->sort('reviewed_by') ?></th>
                    <th><?= $this->Paginator->sort('reviewed_at') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contactMessages as $row): ?>
                <tr>
                    <td><?= $this->Number->format($row->id) ?></td>
                    <td><?= h($row->first_name) ?></td>
                    <td><?= h($row->last_name) ?></td>
                    <td><?= h($row->contact_number) ?></td>
                    <td><?= h($row->email) ?></td>
                    <td><?= h($row->status) ?></td>
                    <td><?= h($row->submitted_at) ?></td>
                    <td><?= $row->reviewed_by === null ? '' : $this->Number->format($row->reviewed_by) ?></td>
                    <td><?= h($row->reviewed_at) ?></td>
                    <td class="actions"><?= $this->element('row_actions', ['id' => $row->id]) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= $this->element('table_pagination') ?>
</div>
