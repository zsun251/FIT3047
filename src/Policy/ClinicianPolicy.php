<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Clinician;
use Authorization\IdentityInterface;

/**
 * ClinicianPolicy
 *
 * Handles authorization for individual Clinician entities.
 * Used when authorizing actions on specific clinician records.
 *
 * Methods in this policy determine:
 * - Who can view a specific clinician
 * - Who can add new clinicians
 * - Who can edit existing clinicians
 * - Who can delete clinicians
 *
 * Note: No CliniciansTablePolicy is needed because there's no query scoping.
 * Only staff can access clinician records, and they see all of them.
 */
class ClinicianPolicy
{
    /**
     * View clinician
     */
    public function canView(IdentityInterface $user, Clinician $resource): bool
    {
        return $this->hasClinicianAccess($user, $resource);
    }

    /**
     * Add clinician (only staff can create)
     */
    public function canAdd(IdentityInterface $user, Clinician $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Edit clinician (only staff can edit)
     */
    public function canEdit(IdentityInterface $user, Clinician $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Delete clinician (only staff can delete)
     */
    public function canDelete(IdentityInterface $user, Clinician $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * List clinicians
     */
    public function canIndex(IdentityInterface $user): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Check if user has access to this specific clinician record
     */
    private function hasClinicianAccess(IdentityInterface $user, Clinician $resource): bool
    {
        // Staff can access all clinician records
        if (UserAccess::isStaff($user)) {
            return true;
        }

        // Users can access their own clinician record
        if ($this->isClinicianOwner($user, $resource)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user is the owner of this clinician record
     */
    private function isClinicianOwner(IdentityInterface $user, Clinician $resource): bool
    {
        $clinicianId = UserAccess::getClinicianId($user);
        return $clinicianId !== null && $clinicianId === $resource->id;
    }
}
