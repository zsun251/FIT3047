<?php
/**
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Forgot Password');
echo $this->Html->css('login', ['block' => true]);
?>

<div class="login-container">
    <div class="login-card">
        <!-- Logo/Brand -->
        <div class="login-brand">
            <i class="bi bi-heart-pulse-fill"></i>
            <h1><?= $this->ContentBlock->text('global-site-name') ?></h1>
            <p>Respiratory Health Management</p>
        </div>

        <!-- Form -->
        <div class="login-form">
            <h2><?= $this->ContentBlock->text('auth-forgot-password-heading') ?></h2>
            <p class="login-subtitle"><?= $this->ContentBlock->text('auth-forgot-password-intro-text') ?></p>

            <?= $this->Form->create(null, ['class' => 'needs-validation']) ?>

            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="bi bi-envelope"></i> Email Address
                </label>
                <?= $this->Form->control('email', [
                    'label' => false,
                    'type' => 'email',
                    'class' => 'form-control',
                    'placeholder' => 'Enter your email address',
                    'required' => true,
                    'autofocus' => true
                ]) ?>
            </div>

            <button type="submit" class="btn btn-primary btn-login">
                <i class="bi bi-send"></i> Send Reset Link
            </button>

            <?= $this->Form->end() ?>

            <!-- Back to Login -->
            <div class="login-links">
                <?= $this->Html->link(
                    '<i class="bi bi-arrow-left"></i> Back to Login',
                    ['action' => 'login'],
                    ['escape' => false, 'class' => 'login-link']
                ) ?>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="login-info">
            <p>
                <i class="bi bi-shield-check"></i>
                Your data is secure and encrypted
            </p>
        </div>
    </div>
</div>
