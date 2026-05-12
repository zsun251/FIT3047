<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Reminder;
use Authorization\IdentityInterface;
use Cake\Datasource\FactoryLocator;

/**
 * ReminderPolicy
 *
 * Handles authorization for reminder records.
 *
 * Access rules:
 * - Patients can only view and manage their own reminders
 * - Patients can view the index of their reminders
 * - Patients can configure their own reminder settings
 * - Patients can dismiss their own reminders
 * - Staff cannot access patient reminders (privacy)
 */
class ReminderPolicy
{
    /**
     * Access reminders index (list of reminders).
     * Only patients can view their own reminders.
     */
    public function canIndex(IdentityInterface $user, Reminder $resource): bool
    {
        return UserAccess::isPatient($user);
    }

    /**
     * Access active reminders API endpoint.
     * Only patients can access their active reminders.
     */
    public function canActive(IdentityInterface $user, Reminder $resource): bool
    {
        return UserAccess::isPatient($user);
    }

    /**
     * Dismiss a reminder.
     * Only the patient who owns the reminder can dismiss it.
     */
    public function canDismiss(IdentityInterface $user, Reminder $resource): bool
    {
        if (!UserAccess::isPatient($user)) {
            return false;
        }

        return $this->isOwnReminder($user, $resource);
    }

    /**
     * Access reminder count API endpoint.
     * Only patients can access their reminder count.
     */
    public function canCount(IdentityInterface $user, Reminder $resource): bool
    {
        return UserAccess::isPatient($user);
    }

    /**
     * Access reminder settings page.
     * Only patients can configure their own reminder settings.
     */
    public function canSettings(IdentityInterface $user, Reminder $resource): bool
    {
        return UserAccess::isPatient($user);
    }

    /**
     * Check if this reminder belongs to the logged-in patient.
     */
    private function isOwnReminder(IdentityInterface $user, Reminder $resource): bool
    {
        $patientId = UserAccess::getPatientId($user);

        if ($patientId === null) {
            return false;
        }

        // Check if this reminder is linked to the patient via vitals_reminders
        return FactoryLocator::get('Table')
            ->get('VitalsReminders')
            ->exists([
                'VitalsReminders.reminder_id' => $resource->id,
                'VitalsReminders.patient_id' => $patientId,
            ]);
    }
}
