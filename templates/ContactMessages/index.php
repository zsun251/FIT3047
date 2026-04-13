<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\ContactMessage> $contactMessages
 */
?>
<?php $this->assign('title', 'Contact Us') ?>
<div class="contactMessages index content">
    <?= $this->Html->link(__('New Contact Message'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Contact Messages') ?></h3>
    <div class="table-responsive">
        <table>
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
                <?php foreach ($contactMessages as $contactMessage): ?>
                <tr>
                    <td><?= $this->Number->format($contactMessage->id) ?></td>
                    <td><?= h($contactMessage->first_name) ?></td>
                    <td><?= h($contactMessage->last_name) ?></td>
                    <td><?= h($contactMessage->contact_number) ?></td>
                    <td><?= h($contactMessage->email) ?></td>
                    <td><?= h($contactMessage->status) ?></td>
                    <td><?= h($contactMessage->submitted_at) ?></td>
                    <td><?= $contactMessage->reviewed_by === null ? '' : $this->Number->format($contactMessage->reviewed_by) ?></td>
                    <td><?= h($contactMessage->reviewed_at) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $contactMessage->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $contactMessage->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $contactMessage->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $contactMessage->id),
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
