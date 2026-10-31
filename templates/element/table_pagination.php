<?php
/**
 * Pagination controls shown under a data table.
 *
 * @var \App\View\AppView $this
 */
?>
<div class="rc-pagination">
    <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    <ul class="pagination">
        <?= $this->Paginator->prev(__('Previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('Next')) ?>
    </ul>
</div>
