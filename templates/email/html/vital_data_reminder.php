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
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
<div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <div style="background: #0d6efd; color: white; padding: 30px 20px; text-align: center;">
        <?= $this->Html->image('logo.png', [
            'alt' => 'RespiraCare Logo',
            'style' => 'max-width: 200px; height: auto; margin-bottom: 15px;',
            'fullBase' => true
        ]) ?>
        <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 16px;">Health Data Reminder</p>
    </div>

    <div style="padding: 30px;">
        <p>Hello <strong><?= h($user->first_name) ?></strong>,</p>

        <p>This is your scheduled reminder to log your vital health data for today.</p>

        <p>Taking a few minutes to record your health measurements helps you and your care team track your respiratory health over time.</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="<?= $vitalDataUrl ?>" style="display: inline-block; padding: 14px 35px; background: #0d6efd; color: white !important; text-decoration: none; border-radius: 6px; font-weight: 600;">📊 Log Health Data Now</a>
        </div>

        <p><strong>Or copy and paste this link into your browser:</strong></p>
        <div style="word-break: break-all; color: #0d6efd; font-size: 14px; background: #f8f9fa; padding: 10px; border-radius: 4px; border: 1px solid #dee2e6;">
            <?= h($vitalDataUrl) ?>
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin-top: 20px; font-size: 13px; color: #495057;">
            <strong>💡 Quick Tip:</strong><br>
            Log your measurements at the same time each day for the most accurate tracking. This reminder is based on your personal schedule preferences.
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e9ecef; font-size: 14px; color: #6c757d;">
            <p style="margin: 10px 0;"><strong>Stay healthy,</strong><br>The RespiraCare Team</p>

            <p style="font-size: 12px; color: #868e96; margin-top: 20px;">
                You can manage your reminder schedule in your <a href="<?= $this->Url->build(['controller' => 'Reminders', 'action' => 'settings'], ['fullBase' => true]) ?>" style="color: #0d6efd;">reminder settings</a>.<br>
                This is an automated reminder. Please do not reply to this message.
            </p>
        </div>
    </div>
</div>
</body>
</html>
