<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Patient;
use Authorization\IdentityInterface;

/**
 * PatientPolicy
 *
 * Handles authorization for individual Patient entities.
 * Used when authorizing actions on specific patient records.
 *
 * Methods in this policy determine:
 * - Who can view a specific patient
 * - Who can add new patients
 * - Who can edit existing patients
 * - Who can delete patients
 *
 * Note: No PatientsTablePolicy is needed because there's no query scoping.
 * Only staff can access patient records, and they see all of them.
 */
class PatientPolicy
{
    /**
     * View patient
     */
    public function canView(IdentityInterface $user, Patient $resource): bool
    {
        return $this->hasPatientAccess($user, $resource);
    }

    /**
     * Add patient (only staff can create)
     */
    public function canAdd(IdentityInterface $user, Patient $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Edit patient (only staff can edit)
     */
    public function canEdit(IdentityInterface $user, Patient $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Delete patient (only staff can delete)
     */
    public function canDelete(IdentityInterface $user, Patient $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * List patients
     */
    public function canIndex(IdentityInterface $user): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Check if user has access to this specific patient record
     */
    private function hasPatientAccess(IdentityInterface $user, Patient $resource): bool
    {
        // Staff can access all patient records
        if (UserAccess::isStaff($user)) {
            return true;
        }

        // Users can access their own patient record
        if ($this->isPatientOwner($user, $resource)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user is the owner of this patient record
     */
    private function isPatientOwner(IdentityInterface $user, Patient $resource): bool
    {
        $patientId = UserAccess::getPatientId($user);
        return $patientId !== null && $patientId === $resource->id;
    }
}
