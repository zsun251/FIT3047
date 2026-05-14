<?php
/**
 * @var \App\View\AppView $this
 */

$this->assign('title', 'Login');
$this->Html->css('auth', ['block' => true]);
?>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-brand-panel">
            <?= $this->Html->image('login image.png', [
                'alt' => 'RespiraCare logo',
                'class' => 'auth-logo-img'
            ]) ?>

            <h1 class="auth-brand-name">RespiraCare</h1>
        </div>

        <div class="auth-form-panel">
            <div class="auth-heading">
                <h2>Welcome Back</h2>
                <p>Sign in to access your account</p>
            </div>

            <?= $this->Flash->render() ?>

            <?= $this->Form->create(null, [
                'class' => 'auth-form'
            ]) ?>

            <div class="auth-field">
                <label for="username" class="auth-label">
                    <i class="bi bi-person"></i>
                    Username or Email
                </label>

                <?= $this->Form->control('username', [
                    'type' => 'text',
                    'label' => false,
                    'placeholder' => 'Enter your username or email',
                    'class' => 'form-control auth-input',
                    'id' => 'username',
                    'required' => true,
                    'autocomplete' => 'username'
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
                    'placeholder' => 'Enter your password',
                    'class' => 'form-control auth-input',
                    'id' => 'password',
                    'required' => true,
                    'autocomplete' => 'current-password'
                ]) ?>
            </div>

            <?= $this->Form->button(
                '<i class="bi bi-box-arrow-in-right"></i> Sign In',
                [
                    'class' => 'btn btn-primary auth-submit',
                    'escapeTitle' => false
                ]
            ) ?>

            <?= $this->Form->end() ?>

            <div class="auth-divider"></div>

            <div class="auth-link-row">
                <?= $this->Html->link(
                    '<i class="bi bi-key"></i> Forgot Password?',
                    ['controller' => 'Users', 'action' => 'forgotPassword'],
                    [
                        'escape' => false,
                        'class' => 'auth-link'
                    ]
                ) ?>
            </div>

            <div class="auth-divider"></div>

            <div class="auth-register-row">
                <span>Don't have an account?</span>
                <?= $this->Html->link(
                    'Register here',
                    ['controller' => 'Users', 'action' => 'register'],
                    ['class' => 'auth-link']
                ) ?>
            </div>
        </div>
    </div>
</div>
