<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Symptom> $symptoms
 */

$this->assign('title', __('Symptoms'));
?>
<?= $this->element('page_header', [
    'title' => __('Symptoms'),
    'subtitle' => __('Manage the symptoms patients can report'),
    'backUrl' => ['controller' => 'Dashboard', 'action' => 'staff'],
    'action' => $this->Html->link(
        '<i class="bi bi-plus-circle"></i><span>' . __('New Symptom') . '</span>',
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
                    <th><?= $this->Paginator->sort('symptom_name') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($symptoms as $row): ?>
                <tr>
                    <td><?= $this->Number->format($row->id) ?></td>
                    <td><?= h($row->symptom_name) ?></td>
                    <td class="actions"><?= $this->element('row_actions', ['id' => $row->id]) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= $this->element('table_pagination') ?>
</div>
