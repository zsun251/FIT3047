<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\User;
use Authorization\IdentityInterface;

class UserPolicy
{

    /**
     * View single user
     */
    public function canView(IdentityInterface $user, User $resource): bool
    {
        return UserAccess::isStaff($user)
            || UserAccess::isOwner($user, $resource);
    }

    /**
     * Add user (only staff)
     */
    public function canAdd(IdentityInterface $user, User $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Edit user
     */
    public function canEdit(IdentityInterface $user, User $resource): bool
    {
        return UserAccess::isStaff($user)
            || UserAccess::isOwner($user, $resource);
    }

    /**
     * Delete user
     */
    public function canDelete(IdentityInterface $user, User $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Listing users (index)
     */
    public function canIndex(IdentityInterface $user, User $resource): bool
    {
        return UserAccess::isStaff($user);
    }
}
