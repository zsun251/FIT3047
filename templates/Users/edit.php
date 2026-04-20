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
                echo $this->Form->control('password', [
                    'type' => 'password',
                    'required' => true,
                    'minlength' => 8,
                    'maxlength' => 255,
                    'autocomplete' => 'new-password',]);
                echo $this->Form->control('first_name', [
                    'required' => true,
                    'maxlength' => 100,
                    'pattern' => '[A-Za-z- ]+',
                    'title' => 'First name can only contain letters, spaces and hyphens.',
                    'autocomplete' => 'given-name',]);
                echo $this->Form->control('last_name', [
                    'required' => true,
                    'maxlength' => 100,
                    'pattern' => '[A-Za-z- ]+',
                    'title' => 'Last name can only contain letters, spaces and hyphens.',
                    'autocomplete' => 'family-name',]);
                echo $this->Form->control('email', [
                    'type' => 'email',
                    'required' => true,
                    'maxlength' => 255,
                    'autocomplete' => 'email',]);
                echo $this->Form->control('phone', [
                    'type' => 'tel',
                    'required' => true,
                    'maxlength' => 20,
                    'pattern' => '[0-9 ]+',
                    'title' => 'Phone number can only contain numbers and spaces.',
                    'autocomplete' => 'tel',]);
                echo $this->Form->control('address', [
                    'type' => 'text',
                    'required' => true,
                    'label' => 'Address',
                    'autocomplete' => 'street-address',
                    'placeholder' => 'Start typing your address',]);
                echo $this->Form->control('date_of_birth', [
                    'empty' => true,
                    'required' => true,
                    'max' => date('Y-m-d'),]);

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
