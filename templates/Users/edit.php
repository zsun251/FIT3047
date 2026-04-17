<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="row">
    <?php if (empty($isPatientSelfEdit)): ?>
        <aside class="column">
            <div class="side-nav">
                <h4 class="heading"><?= __('Actions') ?></h4>
                <?= $this->Form->postLink(
                    __('Delete'),
                    ['action' => 'delete', $user->id],
                    ['confirm' => __('Are you sure you want to delete # {0}?', $user->id), 'class' => 'side-nav-item']
                ) ?>
                <?= $this->Html->link(__('List Users'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            </div>
        </aside>
    <?php endif; ?>
    <div class="column <?= !empty($isPatientSelfEdit) ? 'column-100' : 'column-80' ?>">
        <div class="users form content">
            <?= $this->Form->create($user) ?>
            <fieldset>
                <legend><?= !empty($isPatientSelfEdit) ? __('My Information') : __('Edit User') ?></legend>
                <?php
                echo $this->Form->control('username');
                echo $this->Form->control('password');
                echo $this->Form->control('first_name');
                echo $this->Form->control('last_name');
                echo $this->Form->control('email');
                echo $this->Form->control('phone');
                echo $this->Form->control('address', ['type' => 'text', 'label' => 'Address', 'autocomplete' => 'street-address', 'placeholder' => 'Start typing your address',]);
                echo $this->Form->control('date_of_birth', ['empty' => true]);

                if (empty($isPatientSelfEdit)) {
                    echo $this->Form->control('type');
                    echo $this->Form->control('created_at');
                }
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
