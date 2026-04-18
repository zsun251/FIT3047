<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\IdentityInterface;


/**
 * AppointmentsTablePolicy
 *
 * Handles authorization and query scoping for Appointment collections.
 * Used when applying filters to appointment queries (e.g., in index actions).
 *
 * Scope methods in this policy:
 * - Filter which appointments users can see in lists
 * - Apply role-based query restrictions
 * - Ensure users only see appointments they have access to
 *
 * This is separate from AppointmentPolicy which handles individual entities.
 */
class AppointmentsTablePolicy
{

    /**
     * Scope for index - filter appointments based on user
     */
    public function scopeIndex(IdentityInterface $user, \Cake\ORM\Query $query)
    {
        // Admins see all
        if (UserAccess::isAdmin($user)) {
            return $query;
        }

        if (UserAccess::isClinician($user)) {
            // Clinicians see their appointments
//            $clinicianId = UserAccess::getClinicianId($user);
//            if ($clinicianId !== null) {
//                return $query->where(['Appointments.clinician_id' => $clinicianId]);
//            }
            // Clinicians see all appointments
            return $query;
        }

        // Patients see their appointments
        if (UserAccess::isPatient($user)) {
            $patientId = UserAccess::getPatientId($user);
            if ($patientId !== null) {
                return $query->where(['Appointments.patient_id' => $patientId]);
            }
        }

        // No access - return empty result
        return $query->where(['1 = 0']);
    }

}
