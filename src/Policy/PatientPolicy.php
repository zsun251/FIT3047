<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Patient;
use Authorization\IdentityInterface;

class PatientPolicy
{

    /**
     * View patient
     */
    public function canView(IdentityInterface $user, Patient $resource): bool
    {
        return UserAccess::isStaff($user)
            || UserAccess::isOwner($user, $resource);
    }

    /**
     * Add patient
     */
    public function canAdd(IdentityInterface $user, Patient $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Edit patient
     */
    public function canEdit(IdentityInterface $user, Patient $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Delete patient
     */
    public function canDelete(IdentityInterface $user, Patient $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * List patients
     */
    public function canIndex(IdentityInterface $user, Patient $resource): bool
    {
        return UserAccess::isStaff($user);
    }
}
