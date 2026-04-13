<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Clinician> $clinicians
 */
?>
<div class="clinicians index content">
    <?= $this->Html->link(__('New Clinician'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Clinicians') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clinicians as $clinician): ?>
                <tr>
                    <td><?= $this->Number->format($clinician->id) ?></td>
                    <td><?= $clinician->hasValue('user') ? $this->Html->link($clinician->user->username, ['controller' => 'Users', 'action' => 'view', $clinician->user->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $clinician->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $clinician->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $clinician->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $clinician->id),
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