<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\IdentityInterface;
use Cake\ORM\Query;

/**
 * VitalDataTablePolicy
 *
 * Handles authorization and query scoping for VitalData collections.
 * Used when applying filters to vital data queries (e.g., in index actions).
 *
 * Scope methods in this policy:
 * - Filter which vital data records users can see in lists
 * - Apply role-based query restrictions
 * - Ensure patients only see their own vital data
 */
class VitalDataTablePolicy
{
    /**
     * Scope the index query to show only vital data the user can access
     *
     * Filtering rules:
     * - Admins: See all vital data
     * - Clinicians: See all vital data
     * - Patients: See only their own vital data
     *
     * @param IdentityInterface $user The user making the request
     * @param Query $query The base query to filter
     * @return Query The filtered query
     */
    public function scopeIndex(IdentityInterface $user, Query $query)
    {
        // Staff see all
        if (UserAccess::isStaff($user)) {
            return $query;
        }

        // Patients see their own vital data
        if (UserAccess::isPatient($user)) {
            $patientId = UserAccess::getPatientId($user);
            if ($patientId !== null) {
                return $query->where(['VitalData.patient_id' => $patientId]);
            }
        }

        // No access - return empty result
        return $query->where(['1 = 0']);
    }
}
