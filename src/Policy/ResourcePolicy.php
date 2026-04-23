<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Resource;
use Authorization\IdentityInterface;

class ResourcePolicy
{

    /**
     * View resource
     */
    public function canView(IdentityInterface $user, Resource $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Add resource
     */
    public function canAdd(IdentityInterface $user, Resource $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Edit resource
     */
    public function canEdit(IdentityInterface $user, Resource $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Delete resource
     */
    public function canDelete(IdentityInterface $user, Resource $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * List resources
     */
    public function canIndex(IdentityInterface $user, Resource $resource): bool
    {
        return UserAccess::isStaff($user);
    }


    /**
     * Read resource
     */
    public function canRead(IdentityInterface $user, Resource $resource): bool
    {
        return UserAccess::isStaff($user) || UserAccess::isPatient($user);
    }
}
