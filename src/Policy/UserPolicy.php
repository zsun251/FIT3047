<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\User;
use Authorization\IdentityInterface;

/**
 * UserPolicy
 *
 * Handles authorization for individual User entities.
 * Used when authorizing actions on specific user records.
 *
 * Methods in this policy determine:
 * - Who can view a specific user
 * - Who can add new users
 * - Who can edit existing users
 * - Who can delete users
 *
 * Note: No UsersTablePolicy is needed because there's no query scoping.
 * Only staff can access user records, and they see all of them.
 */
class UserPolicy
{
    /**
     * View user
     */
    public function canView(IdentityInterface $user, User $resource): bool
    {
        return $this->hasUserAccess($user, $resource);
    }

    /**
     * Add user (only staff can create)
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
        return $this->hasUserAccess($user, $resource);
    }

    /**
     * Delete user (only staff can delete)
     */
    public function canDelete(IdentityInterface $user, User $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * List users
     */
    public function canIndex(IdentityInterface $user): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Check if user has access to this specific user record
     */
    private function hasUserAccess(IdentityInterface $user, User $resource): bool
    {
        // Staff can access all user records
        if (UserAccess::isStaff($user)) {
            return true;
        }

        // Users can access their own user record
        if ($this->isUserOwner($user, $resource)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user is the owner of this user record
     */
    private function isUserOwner(IdentityInterface $user, User $resource): bool
    {
        $userId = UserAccess::getUserId($user);
        return $userId === $resource->id;
    }
}
