<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: #0d6efd;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .button {
            display: inline-block;
            padding: 14px 35px;
            background: #0d6efd;
            color: white !important;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
        }
        .button:hover {
            background: #0b5ed7;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .link-text {
            word-break: break-all;
            color: #0d6efd;
            font-size: 14px;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            font-size: 14px;
            color: #6c757d;
        }
        .footer p {
            margin: 10px 0;
        }
        .security-note {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
            font-size: 13px;
            color: #495057;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <h1>RespiraCare</h1>
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
        <div class="link-text"><?= $resetUrl ?></div>

        <div class="warning">
            <strong>This link will expire in 1 hour</strong> for security reasons.
        </div>

        <div class="security-note">
            <strong>Security Notice:</strong><br>
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
