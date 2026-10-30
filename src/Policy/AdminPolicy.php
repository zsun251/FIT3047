<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Admin;
use App\Model\Entity\User;
use Authorization\IdentityInterface;

class AdminPolicy
{

    /**
     * View single admin
     */
    public function canView(IdentityInterface $user, Admin $resource): bool
    {
        return UserAccess::isAdmin($user);
    }

    /**
     * Add admin (only admins)
     */
    public function canAdd(IdentityInterface $user, Admin $resource): bool
    {
        return UserAccess::isAdmin($user);
    }

    /**
     * Edit admin
     */
    public function canEdit(IdentityInterface $user, Admin $resource): bool
    {
        return UserAccess::isAdmin($user);
    }

    /**
     * Delete admin
     */
    public function canDelete(IdentityInterface $user, Admin $resource): bool
    {
        return UserAccess::isAdmin($user);
    }

    /**
     * Listing admins (index)
     */
    public function canIndex(IdentityInterface $user, Admin $resource): bool
    {
        return UserAccess::isAdmin($user);
    }
}
