<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\I18n\FrozenTime;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * GeneratePatientVitalDataReminders command.
 *
 * Generates daily vital data reminder records for all active patients based on their reminder settings.
 * Should be run daily via cron (e.g., at midnight).
 *
 * ## Usage Examples:
 *
 * ### Basic Generation:
 * ```bash
 * # Generate reminders for today
 * bin/cake generate_patient_vital_data_reminders
 *
 * # Generate reminders for specific date
 * bin/cake generate_patient_vital_data_reminders --date 2026-05-15
 * bin/cake generate_patient_vital_data_reminders -d 2026-05-15
 *
 * # Generate reminders for specific patient
 * bin/cake generate_patient_vital_data_reminders --patient-id 1
 * bin/cake generate_patient_vital_data_reminders -p 1
 *
 * # Combine options with verbose output
 * bin/cake generate_patient_vital_data_reminders --date 2026-05-15 --patient-id 1 -v
 * ```
 *
 * ### Cleanup Operations:
 * ```bash
 * # Clean up reminders older than 30 days
 * bin/cake generate_patient_vital_data_reminders --clean 30
 * bin/cake generate_patient_vital_data_reminders -c 30
 *
 * # Clean up reminders older than 60 days
 * bin/cake generate_patient_vital_data_reminders --clean 60
 *
 * # Delete ALL reminders (requires confirmation)
 * bin/cake generate_patient_vital_data_reminders --clean-all
 * ```
 *
 * ### Cron Setup:
 * ```bash
 * # Generate reminders daily at 12:01 AM
 * 1 0 * * * /usr/bin/php /path/to/bin/cake.php generate_patient_vital_data_reminders
 *
 * # Clean up old reminders weekly (Sundays at 2 AM)
 * 0 2 * * 0 /usr/bin/php /path/to/bin/cake.php generate_patient_vital_data_reminders --clean 30
 * ```
 *
 * @see https://book.cakephp.org/4/en/console-commands.html
 */
class GeneratePatientVitalDataRemindersCommand extends Command
{
    use LocatorAwareTrait;

    /**
     * Hook method for defining this command's option parser.
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser->setDescription('Generate daily vital data reminders for patients based on their settings.');

        $parser->addOption('date', [
            'short' => 'd',
            'help' => 'Date to generate reminders for (YYYY-MM-DD). Defaults to today.',
            'default' => date('Y-m-d')
        ]);

        $parser->addOption('patient-id', [
            'short' => 'p',
            'help' => 'Generate reminders for a specific patient ID only.',
            'default' => null
        ]);

        $parser->addOption('clean', [
            'short' => 'c',
            'help' => 'Clean up old reminders (older than specified days)',
            'boolean' => false,
            'default' => null
        ]);

        $parser->addOption('clean-all', [
            'help' => 'Delete ALL reminders (use with caution!)',
            'boolean' => true,
            'default' => false
        ]);

        $parser->setEpilog([
            '<info>Quick Examples:</info>',
            '',
            '  <comment># Generate for today</comment>',
            '  bin/cake generate_patient_vital_data_reminders',
            '',
            '  <comment># Generate for specific date</comment>',
            '  bin/cake generate_patient_vital_data_reminders --date 2026-05-15',
            '',
            '  <comment># Generate for specific patient with verbose output</comment>',
            '  bin/cake generate_patient_vital_data_reminders --patient-id 1 -v',
            '',
            '  <comment># Clean up reminders older than 30 days</comment>',
            '  bin/cake generate_patient_vital_data_reminders --clean 30',
            '',
            '  <comment># Delete ALL reminders (requires confirmation)</comment>',
            '  bin/cake generate_patient_vital_data_reminders --clean-all',
            '',
            '<info>Options:</info>',
            '  -d, --date        Date to generate for (YYYY-MM-DD)',
            '  -p, --patient-id  Generate for specific patient only',
            '  -c, --clean       Clean reminders older than N days',
            '  --clean-all       Delete ALL reminders (dangerous!)',
            '  -v, --verbose     Show detailed output',
            '  -h, --help        Display this help message',
            '',
        ]);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|null The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        // Check if cleanup is requested
        if ($args->getOption('clean-all')) {
            return $this->cleanAllReminders($io);
        }

        if ($args->getOption('clean')) {
            $days = (int)$args->getOption('clean');
            return $this->cleanOldReminders($io, $days);
        }

        // Otherwise, generate reminders
        return $this->generateReminders($args, $io);
    }

    /**
     * Generate reminders for patients
     *
     * @param \Cake\Console\Arguments $args The command arguments
     * @param \Cake\Console\ConsoleIo $io The console IO
     * @return int Exit code
     */
    protected function generateReminders(Arguments $args, ConsoleIo $io): int
    {
        $date = $args->getOption('date');
        $specificPatientId = $args->getOption('patient-id');

        $io->out("<info>===================================</info>");
        $io->out("<info>Patient Vital Data Reminder Generator</info>");
        $io->out("<info>===================================</info>");
        $io->out("Generating reminders for date: <question>{$date}</question>");

        if ($specificPatientId) {
            $io->out("Target patient ID: <question>{$specificPatientId}</question>");
        }
        $io->out("");

        $reminderSettingsTable = $this->fetchTable('ReminderSettings');
        $remindersTable = $this->fetchTable('Reminders');
        $vitalsRemindersTable = $this->fetchTable('VitalsReminders');

        // Build query for active patient reminder settings
        $query = $reminderSettingsTable->find()
            ->where(['ReminderSettings.is_active' => true])
            ->contain(['Patients.Users']);

        // Filter by specific patient if provided
        if ($specificPatientId) {
            $query->where(['ReminderSettings.patient_id' => $specificPatientId]);
        }

        $activeSettings = $query->all();

        if ($activeSettings->isEmpty()) {
            $io->warning('No active reminder settings found.');
            return static::CODE_SUCCESS;
        }

        $totalReminders = 0;
        $totalPatients = 0;
        $skippedReminders = 0;
        $errors = 0;

        foreach ($activeSettings as $settings) {
            $patient = $settings->patient;

            if (!$patient) {
                $io->error("✗ Patient not found for settings ID: {$settings->id}");
                $errors++;
                continue;
            }

            $io->verbose("Processing patient ID: <comment>{$patient->id}</comment> ({$patient->user->first_name} {$patient->user->last_name})");

            // Get all configured time slots
            $timeSlots = $settings->getTimeSlots();

            if (empty($timeSlots)) {
                $io->verbose("  ⊘ No time slots configured");
                continue;
            }

            $remindersCreated = 0;

            foreach ($timeSlots as $timeSlot) {
                // Create scheduled datetime for this reminder
                $scheduledAt = FrozenTime::parse("{$date} {$timeSlot->format('H:i:s')}");

                // Check if reminder already exists for this patient and time
                $exists = $vitalsRemindersTable->find()
                    ->innerJoinWith('Reminders', function ($q) use ($scheduledAt) {
                        return $q->where([
                            'Reminders.scheduled_at' => $scheduledAt,
                            'Reminders.interaction_type IS' => null
                        ]);
                    })
                    ->where(['VitalsReminders.patient_id' => $patient->id])
                    ->count();

                if ($exists > 0) {
                    $io->verbose("  ⊘ Already exists: {$scheduledAt->format('H:i')}");
                    $skippedReminders++;
                    continue;
                }

                // Determine delivery method based on settings
                $deliveryMethod = 'system'; // Default
                if ($settings->email_enabled && $settings->sms_enabled) {
                    $deliveryMethod = 'email'; // Prefer email if both enabled
                } elseif ($settings->email_enabled) {
                    $deliveryMethod = 'email';
                } elseif ($settings->sms_enabled) {
                    $deliveryMethod = 'sms';
                }

                // Create personalized message
                $patientName = $patient->user->first_name ?? 'there';
                $message = "Hi {$patientName}, time to record your vital data for today.";

                // Create the reminder
                $reminder = $remindersTable->newEntity([
                    'message' => $message,
                    'scheduled_at' => $scheduledAt,
                    'status' => 'unsent',
                    'delivery_method' => $deliveryMethod,
                ]);

                if ($remindersTable->save($reminder)) {
                    // Link reminder to patient via vitals_reminders
                    $vitalsReminder = $vitalsRemindersTable->newEntity([
                        'reminder_id' => $reminder->id,
                        'patient_id' => $patient->id,
                    ]);

                    if ($vitalsRemindersTable->save($vitalsReminder)) {
                        $remindersCreated++;
                        $io->verbose("  ✓ Created: {$scheduledAt->format('H:i')} via {$deliveryMethod}");
                    } else {
                        $io->error("  ✗ Failed to link reminder {$reminder->id} to patient {$patient->id}");
                        $errors++;
                        // Rollback: delete the reminder
                        $remindersTable->delete($reminder);
                    }
                } else {
                    $validationErrors = $reminder->getErrors();
                    $io->error("  ✗ Failed to create reminder: " . json_encode($validationErrors));
                    $errors++;
                }
            }

            if ($remindersCreated > 0) {
                $totalPatients++;
                $totalReminders += $remindersCreated;
                $io->success("  ✓ Patient {$patient->id}: Created {$remindersCreated} reminder(s)");
            }
        }

        $io->out("");
        $io->out("<info>===================================</info>");
        $io->out("<info>Generation Summary</info>");
        $io->out("<info>===================================</info>");
        $io->out("Patients processed: <question>{$totalPatients}</question>");
        $io->out("Reminders created: <success>{$totalReminders}</success>");
        $io->out("Reminders skipped: <comment>{$skippedReminders}</comment>");

        if ($errors > 0) {
            $io->out("Errors encountered: <error>{$errors}</error>");
        }

        $io->out("");

        return static::CODE_SUCCESS;
    }

    /**
     * Clean up old reminders
     *
     * @param \Cake\Console\ConsoleIo $io The console IO
     * @param int $days Number of days to keep
     * @return int Exit code
     */
    protected function cleanOldReminders(ConsoleIo $io, int $days): int
    {
        $io->out("<info>===================================</info>");
        $io->out("<info>Reminder Cleanup</info>");
        $io->out("<info>===================================</info>");
        $io->out("Deleting reminders older than <question>{$days} days</question>");
        $io->out("");

        $remindersTable = $this->fetchTable('Reminders');
        $cutoffDate = FrozenTime::now()->subDays($days);

        // Only delete completed or dismissed reminders
        $deleted = $remindersTable->deleteAll([
            'scheduled_at <' => $cutoffDate,
            'interaction_type IS NOT' => null
        ]);

        $io->success("✓ Deleted {$deleted} old reminder(s)");
        $io->out("");

        return static::CODE_SUCCESS;
    }

    /**
     * Delete ALL reminders (dangerous!)
     *
     * @param \Cake\Console\ConsoleIo $io The console IO
     * @return int Exit code
     */
    protected function cleanAllReminders(ConsoleIo $io): int
    {
        $io->out("<error>===================================</error>");
        $io->out("<error>WARNING: DELETE ALL REMINDERS</error>");
        $io->out("<error>===================================</error>");
        $io->out("");

        if (!$io->askChoice('Are you sure you want to delete ALL reminders?', ['y', 'n'], 'n') === 'y') {
            $io->warning('Operation cancelled.');
            return static::CODE_SUCCESS;
        }

        $io->out("");
        $io->out("Type 'DELETE ALL' to confirm:");
        $confirmation = $io->ask('Confirmation');

        if ($confirmation !== 'DELETE ALL') {
            $io->warning('Operation cancelled. Confirmation did not match.');
            return static::CODE_SUCCESS;
        }

        $remindersTable = $this->fetchTable('Reminders');

        $totalCount = $remindersTable->find()->count();
        $io->out("Deleting <error>{$totalCount}</error> reminders...");

        $deleted = $remindersTable->deleteAll(['id IS NOT' => null]);

        $io->success("✓ Deleted {$deleted} reminder(s)");
        $io->out("");

        return static::CODE_SUCCESS;
    }
}
