<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ContactMessage $contactMessage
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $contactMessage->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $contactMessage->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Contact Messages'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="contactMessages form content">
            <?= $this->Form->create($contactMessage) ?>
            <fieldset>
                <legend><?= __('Edit Contact Message') ?></legend>
                <?php
                    echo $this->Form->control('first_name');
                    echo $this->Form->control('last_name');
                    echo $this->Form->control('contact_number');
                    echo $this->Form->control('email');
                    echo $this->Form->control('message');
                    echo $this->Form->control('status');
                    echo $this->Form->control('submitted_at');
                    echo $this->Form->control('reviewed_by');
                    echo $this->Form->control('reviewed_at', ['empty' => true]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
