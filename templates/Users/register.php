<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */

$this->assign('title', 'Register');
$this->Html->css('auth', ['block' => true]);
?>

<div class="auth-page">
    <div class="auth-card auth-card-wide">
        <div class="auth-brand-panel">
            <?= $this->ContentBlock->image('global-logo', [
                'alt' => $this->ContentBlock->text('global-logo-alt'),
                'class' => 'auth-logo-img'
            ]) ?>

            <h1 class="auth-brand-name"><?= $this->ContentBlock->text('global-site-name') ?></h1>
        </div>

        <div class="auth-form-panel">
            <div class="auth-heading">
                <h2><?= $this->ContentBlock->text('auth-register-heading') ?></h2>
                <p><?= $this->ContentBlock->text('auth-register-intro-text') ?></p>
            </div>

            <?= $this->Flash->render() ?>

            <?= $this->Form->create($user ?? null, [
                'class' => 'auth-form'
            ]) ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="auth-field">
                        <label for="first-name" class="auth-label">
                            <i class="bi bi-person"></i>
                            First Name
                        </label>

                        <?= $this->Form->control('first_name', [
                            'label' => false,
                            'class' => 'form-control auth-input',
                            'id' => 'first-name',
                            'autocomplete' => 'given-name',
                            'required' => true
                        ]) ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="auth-field">
                        <label for="last-name" class="auth-label">
                            <i class="bi bi-person"></i>
                            Last Name
                        </label>

                        <?= $this->Form->control('last_name', [
                            'label' => false,
                            'class' => 'form-control auth-input',
                            'id' => 'last-name',
                            'autocomplete' => 'family-name',
                            'required' => true
                        ]) ?>
                    </div>
                </div>
            </div>

            <div class="auth-field">
                <label for="username" class="auth-label">
                    <i class="bi bi-person-badge"></i>
                    Username
                </label>

                <?= $this->Form->control('username', [
                    'label' => false,
                    'class' => 'form-control auth-input',
                    'id' => 'username',
                    'autocomplete' => 'username',
                    'required' => true
                ]) ?>
            </div>

            <div class="auth-field">
                <label for="email" class="auth-label">
                    <i class="bi bi-envelope"></i>
                    Email
                </label>

                <?= $this->Form->control('email', [
                    'type' => 'email',
                    'label' => false,
                    'class' => 'form-control auth-input',
                    'id' => 'email',
                    'autocomplete' => 'email',
                    'required' => true
                ]) ?>
            </div>

            <div class="auth-field">
                <label for="password" class="auth-label">
                    <i class="bi bi-lock"></i>
                    Password
                </label>

                <?= $this->Form->control('password', [
                    'type' => 'password',
                    'label' => false,
                    'class' => 'form-control auth-input',
                    'id' => 'password',
                    'autocomplete' => 'new-password',
                    'required' => true
                ]) ?>
            </div>

            <div class="auth-field">
                <label for="phone-number" class="auth-label">
                    <i class="bi bi-telephone"></i>
                    Phone Number
                </label>

                <?= $this->Form->control('phone_number', [
                    'label' => false,
                    'class' => 'form-control auth-input',
                    'id' => 'phone-number',
                    'autocomplete' => 'tel'
                ]) ?>
            </div>

            <div class="auth-field">
                <label for="date-of-birth" class="auth-label">
                    <i class="bi bi-calendar3"></i>
                    Date of Birth
                </label>

                <?= $this->Form->control('date_of_birth', [
                    'type' => 'date',
                    'label' => false,
                    'class' => 'form-control auth-input',
                    'id' => 'date-of-birth',
                    'autocomplete' => 'bday'
                ]) ?>
            </div>

            <div class="auth-field">
                <label for="address" class="auth-label">
                    <i class="bi bi-geo-alt"></i>
                    Address
                </label>

                <?= $this->Form->control('address', [
                    'type' => 'textarea',
                    'label' => false,
                    'class' => 'form-control auth-input auth-textarea',
                    'id' => 'address',
                    'rows' => 3,
                    'autocomplete' => 'street-address'
                ]) ?>
            </div>

            <?= $this->Form->button(
                '<i class="bi bi-person-plus"></i> Create Account',
                [
                    'class' => 'btn btn-primary auth-submit',
                    'escapeTitle' => false
                ]
            ) ?>

            <?= $this->Form->end() ?>

            <div class="auth-divider"></div>

            <div class="auth-register-row">
                <span>Already have an account?</span>
                <?= $this->Html->link(
                    'Log in',
                    ['controller' => 'Users', 'action' => 'login'],
                    ['class' => 'auth-link']
                ) ?>
            </div>
        </div>
    </div>
</div>
