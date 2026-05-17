<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var string $resetUrl
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $this->Html->css('email', ['fullBase' => true]) ?>
</head>
<body>
<div class="email-container">
    <div class="header">
        <?= $this->Html->image('logo.png', [
            'alt' => 'RespiraCare Logo',
            'class' => 'logo',
            'fullBase' => true
        ]) ?>
        <p>Password Reset Request</p>
    </div>

    <div class="content">
        <p>Hello <strong><?= h($user->first_name) ?></strong>,</p>

        <p>We received a request to reset the password for your RespiraCare account associated with <strong><?= h($user->email) ?></strong>.</p>

        <p>Click the button below to create a new password:</p>

        <div class="button-container">
            <a href="<?= $resetUrl ?>" class="button">Reset My Password</a>
        </div>

        <p><strong>Or copy and paste this link into your browser:</strong></p>
        <div class="link-text"><?= h($resetUrl) ?></div>

        <div class="warning">
            <strong>⏰ This link will expire in 1 hour</strong> for security reasons.
        </div>

        <div class="security-note">
            <strong>🔒 Security Notice:</strong><br>
            If you didn't request this password reset, you can safely ignore this email. Your password will remain unchanged and your account is secure.
        </div>

        <div class="footer">
            <p><strong>Best regards,</strong><br>The RespiraCare Team</p>

            <p style="font-size: 12px; color: #868e96; margin-top: 20px;">
                This is an automated email. Please do not reply to this message.<br>
                If you continue to have problems, please contact your system administrator.
            </p>
        </div>
    </div>
</div>
</body>
</html>
