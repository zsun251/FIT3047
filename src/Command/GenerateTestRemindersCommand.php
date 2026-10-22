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
 * THIS FILE SHOULD NOT BE IN THE PRODUCTION BRANCH
 * GenerateTestReminders command.
 *
 * Generates test reminders at specific intervals relative to current time for testing purposes.
 * Creates reminders: 2 hours ago, 1 hour ago, 5 mins ago, +1 min, +2 min, +3 min, +4 min, +5 min, +2 hours
 */
class GenerateTestRemindersCommand extends Command
{
    use LocatorAwareTrait;

    /**
     * Hook method for defining this command's option parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser->setDescription('Generate test reminders at various times relative to now for testing the reminder display logic.');

        $parser->addOption('patient-id', [
            'short' => 'p',
            'help' => 'Patient ID to generate reminders for (required)',
            'required' => true
        ]);

        $parser->addOption('clean', [
            'short' => 'c',
            'help' => 'Delete existing reminders for today before generating new ones',
            'boolean' => true,
            'default' => false
        ]);

        return $parser;
    }

    /**
     * Execute command
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $patientId = (int)$args->getOption('patient-id');
        $clean = (bool)$args->getOption('clean');

        $io->out("<info>===================================</info>");
        $io->out("<info>Test Reminder Generator</info>");
        $io->out("<info>===================================</info>");
        $io->out("Patient ID: <question>{$patientId}</question>");
        $io->out("Current time: <question>" . FrozenTime::now()->format('Y-m-d H:i:s') . "</question>");
        $io->out("");

        $remindersTable = $this->fetchTable('Reminders');
        $vitalsRemindersTable = $this->fetchTable('VitalsReminders');
        $patientsTable = $this->fetchTable('Patients');

        // Verify patient exists
        if (!$patientsTable->exists(['id' => $patientId])) {
            $io->error("Patient ID {$patientId} does not exist!");
            return static::CODE_ERROR;
        }

        // Clean up existing reminders if requested
        if ($clean) {
            $io->out("<comment>Cleaning up existing reminders for today...</comment>");

            $today = FrozenTime::now()->format('Y-m-d');
            $existingReminders = $remindersTable->find()
                ->matching('VitalsReminders', function ($q) use ($patientId) {
                    return $q->where(['VitalsReminders.patient_id' => $patientId]);
                })
                ->where(['DATE(Reminders.scheduled_at)' => $today])
                ->all();

            $deletedCount = 0;
            foreach ($existingReminders as $reminder) {
                if ($remindersTable->delete($reminder)) {
                    $deletedCount++;
                }
            }

            $io->success("  ✓ Deleted {$deletedCount} existing reminder(s)");
            $io->out("");
        }

        // Define test reminder times relative to now
        $now = FrozenTime::now();

        $testReminders = [
            ['offset' => '-2 hours', 'label' => '2 hours ago (overdue)'],
            ['offset' => '-1 hour', 'label' => '1 hour ago (overdue)'],
            ['offset' => '-30 minutes', 'label' => '30 mins ago (recent)'],
            ['offset' => '-5 minutes', 'label' => '5 mins ago (just passed)'],
            ['offset' => '+1 minute', 'label' => '1 min from now'],
            ['offset' => '+2 minutes', 'label' => '2 mins from now'],
            ['offset' => '+3 minutes', 'label' => '3 mins from now'],
            ['offset' => '+4 minutes', 'label' => '4 mins from now'],
            ['offset' => '+5 minutes', 'label' => '5 mins from now'],
            ['offset' => '+15 minutes', 'label' => '15 mins from now'],
            ['offset' => '+30 minutes', 'label' => '30 mins from now'],
            ['offset' => '+1 hour', 'label' => '1 hour from now'],
            ['offset' => '+2 hours', 'label' => '2 hours from now'],
        ];

        $io->out("<info>Creating test reminders...</info>");
        $io->out("");

        $created = 0;
        $failed = 0;

        foreach ($testReminders as $test) {
            $scheduledAt = FrozenTime::parse($test['offset']);

            // Create the reminder
            $reminder = $remindersTable->newEntity([
                'message' => "Test reminder - {$test['label']}",
                'scheduled_at' => $scheduledAt,
                'status' => 'unsent',
                'delivery_method' => 'system',
            ]);

            if ($remindersTable->save($reminder)) {
                // Link to patient
                $vitalsReminder = $vitalsRemindersTable->newEntity([
                    'reminder_id' => $reminder->id,
                    'patient_id' => $patientId,
                ]);

                if ($vitalsRemindersTable->save($vitalsReminder)) {
                    $created++;
                    $scheduledTime = $scheduledAt->format('H:i:s');
                    $io->success("  ✓ Created: {$scheduledTime} - {$test['label']}");
                } else {
                    $failed++;
                    $io->error("  ✗ Failed to link: {$test['label']}");
                    $remindersTable->delete($reminder);
                }
            } else {
                $failed++;
                $errors = $reminder->getErrors();
                $io->error("  ✗ Failed to create: {$test['label']} - " . json_encode($errors));
            }
        }

        $io->out("");
        $io->out("<info>===================================</info>");
        $io->out("<info>Summary</info>");
        $io->out("<info>===================================</info>");
        $io->out("Reminders created: <success>{$created}</success>");

        if ($failed > 0) {
            $io->out("Failed: <error>{$failed}</error>");
        }

        $io->out("");
        $io->out("<comment>Test your reminder display at:</comment>");
        $io->out("http://localhost/reminders");
        $io->out("");
        $io->out("<comment>Expected behavior:</comment>");
        $io->out("  • <info>Active Reminders:</info> 2 hrs ago, 1 hr ago, 30 mins ago, 5 mins ago");
        $io->out("  • <info>Upcoming Today:</info> 1m, 2m, 3m, 4m, 5m, 15m, 30m, 1h, 2h from now");
        $io->out("");

        return static::CODE_SUCCESS;
    }
}
