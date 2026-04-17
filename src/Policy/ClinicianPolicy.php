<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Clinician;
use Authorization\IdentityInterface;

class ClinicianPolicy
{

    /**
     * View clinician
     */
    public function canView(IdentityInterface $user, Clinician $resource): bool
    {
        return UserAccess::isStaff($user)
            || UserAccess::isOwner($user, $resource);
    }

    /**
     * Add clinician
     */
    public function canAdd(IdentityInterface $user, Clinician $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Edit clinician
     */
    public function canEdit(IdentityInterface $user, Clinician $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Delete clinician
     */
    public function canDelete(IdentityInterface $user, Clinician $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Listing clinicians
     */
    public function canIndex(IdentityInterface $user, Clinician $resource): bool
    {
        return UserAccess::isStaff($user);
    }
}
