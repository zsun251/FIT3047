<?php
/**
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Login');

// Add login-specific CSS
echo $this->Html->css('login', ['block' => true]);
?>

<div class="login-container">
    <div class="login-card">
        <!-- Logo/Brand -->
        <div class="login-brand">
            <i class="bi bi-heart-pulse-fill"></i>
            <h1>RespiraCare</h1>
        </div>

        <!-- Login Form -->
        <div class="login-form">
            <h2>Welcome Back</h2>
            <p class="login-subtitle">Sign in to access your account</p>

            <?= $this->Form->create(null, [
                'class' => 'needs-validation',
                'novalidate' => true
            ]) ?>

            <div class="form-group">
                <label for="username" class="form-label">
                    <i class="bi bi-person"></i> Username or Email
                </label>
                <?= $this->Form->control('username', [
                    'label' => false,
                    'class' => 'form-control',
                    'placeholder' => 'Enter your username or email',
                    'required' => true,
                    'autocomplete' => 'username',
                    'autofocus' => true
                ]) ?>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="bi bi-lock"></i> Password
                </label>
                <?= $this->Form->control('password', [
                    'label' => false,
                    'class' => 'form-control',
                    'placeholder' => 'Enter your password',
                    'required' => true,
                    'autocomplete' => 'current-password'
                ]) ?>
            </div>


            <button type="submit" class="btn btn-primary btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </button>

            <?= $this->Form->end() ?>

            <!-- Additional Links -->
            <div class="login-links">
                <?= $this->Html->link(
                    '<i class="bi bi-key"></i> Forgot Password?',
                    ['controller' => 'Users', 'action' => 'forgotPassword'],
                    ['escape' => false, 'class' => 'login-link']
                ) ?>
            </div>

            <!-- Registration Link -->
            <div class="login-footer">
                <p>Don't have an account?
                    <?= $this->Html->link(
                        'Register here',
                        ['controller' => 'Users', 'action' => 'register'],
                        ['class' => 'login-link']
                    ) ?>
                </p>
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
