<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var string $vitalDataUrl
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
        <p>Health Data Reminder</p>
    </div>

    <div class="content">
        <p>Hello <strong><?= h($user->first_name) ?></strong>,</p>

        <p>This is your scheduled reminder to log your vital health data for today.</p>

        <p>Taking a few minutes to record your health measurements helps you and your care team track your respiratory health over time.</p>

        <div class="button-container">
            <a href="<?= $vitalDataUrl ?>" class="button">📊 Log Health Data Now</a>
        </div>

        <p><strong>Or copy and paste this link into your browser:</strong></p>
        <div class="link-text"><?= h($vitalDataUrl) ?></div>

        <div class="security-note">
            <strong>💡 Quick Tip:</strong><br>
            Log your measurements at the same time each day for the most accurate tracking. This reminder is based on your personal schedule preferences.
        </div>

        <div class="footer">
            <p><strong>Stay healthy,</strong><br>The RespiraCare Team</p>

            <p style="font-size: 12px; color: #868e96; margin-top: 20px;">
                You can manage your reminder schedule in your <a href="<?= $this->Url->build(['controller' => 'Reminders', 'action' => 'settings'], ['fullBase' => true]) ?>" style="color: #0d6efd;">reminder settings</a>.<br>
                This is an automated reminder. Please do not reply to this message.
            </p>
        </div>
    </div>
</div>
</body>
</html>
