<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\IdentityInterface;
use Cake\ORM\Query\SelectQuery;

/**
 * RemindersTablePolicy
 *
 * Handles authorization query scoping for reminder collections.
 *
 * Scope rules:
 * - Only patients can access reminders
 * - Patients can only see their own reminders
 * - Staff cannot access patient reminders (privacy)
 */
class RemindersTablePolicy
{
    /**
     * Scope the index query to reminders the patient can access.
     *
     * Only patients can view reminders, and they can only see their own.
     *
     * @param \Authorization\IdentityInterface $user The user making the request
     * @param \Cake\ORM\Query\SelectQuery $query The base query to filter
     * @return \Cake\ORM\Query\SelectQuery The filtered query
     */
    public function scopeIndex(IdentityInterface $user, SelectQuery $query): SelectQuery
    {
        // Only patients can access reminders
        if (!UserAccess::isPatient($user)) {
            return $query->where(['1 = 0']);
        }

        $patientId = UserAccess::getPatientId($user);

        if ($patientId === null) {
            return $query->where(['1 = 0']);
        }

        // Join with vitals_reminders to filter by patient_id
        return $query
            ->innerJoinWith('VitalsReminders', function ($q) use ($patientId) {
                return $q->where(['VitalsReminders.patient_id' => $patientId]);
            });
    }

    /**
     * Scope the active reminders query.
     *
     * @param \Authorization\IdentityInterface $user The user making the request
     * @param \Cake\ORM\Query\SelectQuery $query The base query to filter
     * @return \Cake\ORM\Query\SelectQuery The filtered query
     */
    public function scopeActive(IdentityInterface $user, SelectQuery $query): SelectQuery
    {
        if (!UserAccess::isPatient($user)) {
            return $query->where(['1 = 0']);
        }

        $patientId = UserAccess::getPatientId($user);

        if ($patientId === null) {
            return $query->where(['1 = 0']);
        }

        return $query
            ->innerJoinWith('VitalsReminders', function ($q) use ($patientId) {
                return $q->where([
                    'VitalsReminders.patient_id' => $patientId,
                    'VitalsReminders.is_active' => true,
                ]);
            })
            ->where([
                'Reminders.scheduled_at <=' => new \DateTime(),
                'Reminders.interaction_type IS' => null,
            ]);
    }
}
