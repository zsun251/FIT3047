<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\I18n\DateTime; // Changed from FrozenTime
use Cake\Mailer\Mailer;
use Cake\Routing\Router;

class SendVitalDataRemindersCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $io->out('Checking for reminders to send...');

        $remindersTable = $this->fetchTable('Reminders');
        $now = DateTime::now();

        // Get all pending reminders scheduled for this exact minute
        // Round to the minute to match scheduled times
        $currentMinute = $now->second(0)->microsecond(0);
        $nextMinute = $currentMinute->modify('+1 minute');

        $io->verbose("Looking for reminders between {$currentMinute->i18nFormat('yyyy-MM-dd HH:mm:ss')} and {$nextMinute->i18nFormat('yyyy-MM-dd HH:mm:ss')}");

        $pendingReminders = $remindersTable->find()
            ->contain(['VitalsReminders.Patients.Users'])
            ->where([
                'Reminders.scheduled_at >=' => $currentMinute,
                'Reminders.scheduled_at <' => $nextMinute,
                'Reminders.interaction_type IS' => null,
                'Reminders.status' => 'pending'
            ])
            ->all();

        if ($pendingReminders->isEmpty()) {
            $io->out('No reminders to send at this time.');
            return static::CODE_SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        foreach ($pendingReminders as $reminder) {
            $vitalReminder = $reminder->vitals_reminders[0] ?? null;

            if (!$vitalReminder || !$vitalReminder->patient) {
                $io->warning("Skipping reminder {$reminder->id}: No patient found");
                $failed++;
                continue;
            }

            $patient = $vitalReminder->patient;
            $user = $patient->user;

            if (!$user || !$user->email) {
                $io->warning("Skipping reminder {$reminder->id}: No user email found");
                $failed++;
                continue;
            }

            // Generate vital data URL
            $vitalDataUrl = Router::url([
                'controller' => 'VitalData',
                'action' => 'add',
                '?' => ['reminder_id' => $reminder->id]
            ], true);

            try {
                $mailer = new Mailer('default');
                $mailer
                    ->setEmailFormat('both')
                    ->setTo($user->email) // Send to actual user
                    ->setSubject('Reminder: Log your health data');

                $mailer
                    ->viewBuilder()
                    ->setTemplate('vital_data_reminder');

                $mailer->setViewVars([
                    'user' => $user,
                    'vitalDataUrl' => $vitalDataUrl
                ]);

                $mailer->deliver();

                // Mark as sent
                $reminder->status = 'sent';
                $remindersTable->save($reminder);

                $sent++;
                $io->success("✓ Sent reminder to {$user->email}");

            } catch (\Exception $e) {
                $failed++;
                $io->error("✗ Failed to send to {$user->email}: " . $e->getMessage());
                \Cake\Log\Log::error("Reminder email failed for reminder {$reminder->id}: " . $e->getMessage());
            }
        }

        $io->out('');
        $io->out("Summary: {$sent} sent, {$failed} failed");

        return static::CODE_SUCCESS;
    }
}
