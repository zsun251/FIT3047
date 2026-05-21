<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Reminder;
use Authorization\IdentityInterface;

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
    public function canIndex(IdentityInterface $user, Reminder $reminder)
    {
        // Only patients can view reminders
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
    public function canDismiss(IdentityInterface $user, Reminder $reminder)
    {
        // Only patients can dismiss reminders
        if (!UserAccess::isPatient($user)) {
            return false;
        }

        // If the reminder has an ID, check ownership through vitals_reminders
        if ($reminder->id) {
            return $this->isOwnReminder($user, $reminder->id);
        }

        // New empty entities can be checked (for initial authorization)
        return true;
    }

    /**
     * Access reminder count API endpoint.
     * Only patients can access their reminder count.
     */
    public function canSettings(IdentityInterface $user, Reminder $reminder)
    {
        // Only patients can access reminder settings
        return UserAccess::isPatient($user);
    }

    /**
     * Access reminder settings page.
     * Only patients can configure their own reminder settings.
     */
    public function canCount(IdentityInterface $user, Reminder $reminder)
    {
        // Only patients can get reminder count
        return UserAccess::isPatient($user);
    }

    /**
     * Check if user can access dropdown endpoint
     */
    public function canDropdown(IdentityInterface $user, Reminder $reminder)
    {
        // Only patients can get reminder dropdown
        return UserAccess::isPatient($user);
    }

    /**
     * Check if a reminder belongs to the patient through vitals_reminders junction
     *
     * @param IdentityInterface $user
     * @param int $reminderId
     * @return bool
     */
    protected function isOwnReminder(IdentityInterface $user, int $reminderId): bool
    {
        $patientId = UserAccess::getPatientId($user);

        if (!$patientId) {
            return false;
        }

        // Check in the vitals_reminders junction table
        $vitalsRemindersTable = \Cake\ORM\TableRegistry::getTableLocator()->get('VitalsReminders');

        return $vitalsRemindersTable->exists([
            'VitalsReminders.reminder_id' => $reminderId,
            'VitalsReminders.patient_id' => $patientId
        ]);
    }
}
