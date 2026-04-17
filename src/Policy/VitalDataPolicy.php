<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\VitalData;
use Authorization\IdentityInterface;

class VitalDataPolicy
{

    /**
     * View vital data
     */
    public function canView(IdentityInterface $user, VitalData $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Add vital data
     */
    public function canAdd(IdentityInterface $user, VitalData $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Edit vital data
     */
    public function canEdit(IdentityInterface $user, VitalData $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Delete vital data
     */
    public function canDelete(IdentityInterface $user, VitalData $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * List vital data
     */
    public function canIndex(IdentityInterface $user, VitalData $resource): bool
    {
        return UserAccess::isStaff($user);
    }
}
