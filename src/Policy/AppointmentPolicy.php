<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Admin;
use Authorization\IdentityInterface;

/**
 * AdminPolicy
 *
 * Handles authorization for individual Admin entities.
 * Used when authorizing actions on specific admin records.
 *
 * Methods in this policy determine:
 * - Who can view a specific admin
 * - Who can add new admins
 * - Who can edit existing admins
 * - Who can delete admins
 *
 * Note: No AdminsTablePolicy is needed because there's no query scoping.
 * Only staff can access admin records, and they see all of them.
 */
class AdminPolicy
{
    /**
     * View admin
     */
    public function canView(IdentityInterface $user, Admin $resource): bool
    {
        return $this->hasAdminAccess($user, $resource);
    }

    /**
     * Add admin (only staff can create)
     */
    public function canAdd(IdentityInterface $user, Admin $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Edit admin (only staff can edit)
     */
    public function canEdit(IdentityInterface $user, Admin $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Delete admin (only staff can delete)
     */
    public function canDelete(IdentityInterface $user, Admin $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * List admins
     */
    public function canIndex(IdentityInterface $user): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Check if user has access to this specific admin record
     */
    private function hasAdminAccess(IdentityInterface $user, Admin $resource): bool
    {
        // Staff can access all admin records
        if (UserAccess::isStaff($user)) {
            return true;
        }

        // Users can access their own admin record
        if ($this->isAdminOwner($user, $resource)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user is the owner of this admin record
     */
    private function isAdminOwner(IdentityInterface $user, Admin $resource): bool
    {
        $adminId = UserAccess::getAdminId($user);
        return $adminId !== null && $adminId === $resource->id;
    }
}
