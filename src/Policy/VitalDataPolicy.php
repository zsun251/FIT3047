<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\VitalData;
use Authorization\IdentityInterface;

/**
 * VitalDataPolicy
 *
 * Handles authorization for individual VitalData entities.
 * Used when authorizing actions on specific vital data records.
 *
 * Methods in this policy determine:
 * - Who can view a specific vital data record
 * - Who can add new vital data records
 * - Who can edit existing vital data records
 * - Who can delete vital data records
 * - Who can access the patient history page
 *
 * Access rules:
 * - Staff can view/manage all vital data
 * - Patients can view and add their own vital data
 * - Patients can access their own vital data history page
 */
class VitalDataPolicy
{
    /**
     * View vital data
     */
    public function canView(IdentityInterface $user, VitalData $resource): bool
    {
        return $this->hasVitalDataAccess($user, $resource);
    }

    /**
     * Add vital data
     */
    public function canAdd(IdentityInterface $user, VitalData $resource): bool
    {
        return UserAccess::isStaff($user) || UserAccess::isPatient($user);
    }

    /**
     * Edit vital data (only staff can edit)
     */
    public function canEdit(IdentityInterface $user, VitalData $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Delete vital data (only staff can delete)
     */
    public function canDelete(IdentityInterface $user, VitalData $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * List vital data
     */
    public function canIndex(IdentityInterface $user): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Access the patient history page
     *
     * This is a page-level permission check, so it should allow
     * authenticated users who are permitted to open the history page.
     * The actual records shown on that page are filtered in the table policy.
     */
    public function canViewHistory(IdentityInterface $user, VitalData $resource): bool
    {
        return UserAccess::isStaff($user) || UserAccess::isPatient($user);
    }

    /**
     * Check if user has access to this specific vital data record
     */
    private function hasVitalDataAccess(IdentityInterface $user, VitalData $resource): bool
    {
        // Staff can access all vital data
        if (UserAccess::isStaff($user)) {
            return true;
        }

        // Patients can access their own vital data
        if ($this->isOwnVitalData($user, $resource)) {
            return true;
        }

        return false;
    }

    /**
     * Check if this vital data belongs to the user (as a patient)
     */
    private function isOwnVitalData(IdentityInterface $user, VitalData $resource): bool
    {
        $patientId = UserAccess::getPatientId($user);
        return $patientId !== null && $patientId === $resource->patient_id;
    }
}
