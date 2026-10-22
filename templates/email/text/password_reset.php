<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var string $resetUrl
 */
?>
=====================================
RespiraCare - Password Reset Request
=====================================

Hello <?= $user->first_name ?>,

We received a request to reset the password for your RespiraCare account associated with <?= $user->email ?>.

Click or copy this link to create a new password:

<?= $resetUrl ?>


IMPORTANT: This link will expire in 1 hour for security reasons.

---

SECURITY NOTICE:
If you didn't request this password reset, you can safely ignore this email. Your password will remain unchanged and your account is secure.

---

Best regards,
The RespiraCare Team

This is an automated email. Please do not reply to this message.
If you continue to have problems, please contact your system administrator.
