<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var string $vitalDataUrl
 */
?>
=====================================
RespiraCare - Health Data Reminder
=====================================

Hello <?= $user->first_name ?>,

This is your scheduled reminder to log your vital health data for today.

Taking a few minutes to record your health measurements helps you and your care team track your respiratory health over time.

Click or copy this link to log your data:

<?= $vitalDataUrl ?>


QUICK TIP:
Log your measurements at the same time each day for the most accurate tracking. This reminder is based on your personal schedule preferences.

---

You can manage your reminder schedule in your reminder settings:
<?= $this->Url->build(['controller' => 'Reminders', 'action' => 'settings'], ['fullBase' => true]) ?>
---

Stay healthy,
The RespiraCare Team

This is an automated reminder. Please do not reply to this message.
