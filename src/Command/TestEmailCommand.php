<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Mailer\Mailer;

class TestEmailCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $io->out('Testing email configuration with template...');

        try {
            // Create mailer with default config
            $mailer = new Mailer('default');

            // Basic email config
            $mailer
                ->setEmailFormat('both')
                ->setTo('mhstoeg@gmail.com') // Your test email
                ->setSubject('Test: Reset your account password');

            // Select email template
            $mailer
                ->viewBuilder()
                ->setTemplate('reset_password');

            // Transfer required view variables to email template
            $mailer
                ->setViewVars([
                    'first_name' => 'Mark',
                    'last_name' => 'Sto',
                    'nonce' => 'test-nonce-' . bin2hex(random_bytes(16)),
                    'email' => 'test@example.com',
                ]);

            // Send email
            $result = $mailer->deliver();

            if ($result) {
                $io->success('✓ Email sent successfully!');
                return static::CODE_SUCCESS;
            } else {
                $io->error('✗ Email delivery returned false');
                return static::CODE_ERROR;
            }
        } catch (\Exception $e) {
            $io->error('✗ Failed to send email:');
            $io->error($e->getMessage());
            $io->out('');
            $io->out('Stack trace:');
            $io->out($e->getTraceAsString());
            return static::CODE_ERROR;
        }
    }
}
