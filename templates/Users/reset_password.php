<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var string $token
 */
$this->assign('title', 'Reset Password');
echo $this->Html->css('login', ['block' => true]);
?>

<div class="login-container">
    <div class="login-card">
        <!-- Logo/Brand -->
        <div class="login-brand">
            <i class="bi bi-heart-pulse-fill"></i>
            <h1>RespiraCare</h1>
            <p>Respiratory Health Management</p>
        </div>

        <!-- Form -->
        <div class="login-form">
            <h2>Reset Password</h2>
            <p class="login-subtitle">Enter your new password below.</p>

            <?= $this->Form->create(null, ['class' => 'needs-validation']) ?>

            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="bi bi-lock"></i> New Password
                </label>
                <?= $this->Form->control('password', [
                    'label' => false,
                    'class' => 'form-control',
                    'placeholder' => 'Enter new password',
                    'required' => true,
                    'autofocus' => true
                ]) ?>
                <small class="text-muted">Must be at least 8 characters with uppercase, lowercase, and number.</small>
            </div>

            <div class="form-group">
                <label for="confirm-password" class="form-label">
                    <i class="bi bi-lock-fill"></i> Confirm Password
                </label>
                <?= $this->Form->control('confirm_password', [
                    'type' => 'password',
                    'label' => false,
                    'class' => 'form-control',
                    'placeholder' => 'Confirm new password',
                    'required' => true
                ]) ?>
            </div>

            <button type="submit" class="btn btn-primary btn-login">
                <i class="bi bi-check-circle"></i> Reset Password
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
