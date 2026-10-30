<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\User;
use Authorization\IdentityInterface;
use Cake\Datasource\FactoryLocator;

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
 * Only admins can list and manage user accounts. Clinicians can view and edit
 * their assigned patients' accounts, and view other clinicians.
 */
class UserPolicy
{
    /**
     * View user
     */
    public function canView(IdentityInterface $user, User $resource): bool
    {
        if ($this->hasUserAccess($user, $resource)) {
            return true;
        }

        // Clinicians see colleagues on the patient pages
        return UserAccess::isClinician($user) && $resource->type === 'clinician';
    }

    /**
     * Add user (only admins can create)
     */
    public function canAdd(IdentityInterface $user, User $resource): bool
    {
        return UserAccess::isAdmin($user);
    }

    /**
     * Edit user
     */
    public function canEdit(IdentityInterface $user, User $resource): bool
    {
        return $this->hasUserAccess($user, $resource);
    }

    /**
     * Delete user (only admins can delete)
     */
    public function canDelete(IdentityInterface $user, User $resource): bool
    {
        return UserAccess::isAdmin($user);
    }

    /**
     * List users (only admins)
     */
    public function canIndex(IdentityInterface $user): bool
    {
        return UserAccess::isAdmin($user);
    }

    /**
     * Check if user has access to this specific user record
     */
    private function hasUserAccess(IdentityInterface $user, User $resource): bool
    {
        // Admins can access all user records
        if (UserAccess::isAdmin($user)) {
            return true;
        }

        // Users can access their own user record
        if ($this->isUserOwner($user, $resource)) {
            return true;
        }

        // Clinicians can access their assigned patients' accounts
        return $this->isAssignedPatientAccount($user, $resource);
    }

    /**
     * Check if this user record belongs to a patient assigned to the clinician
     */
    private function isAssignedPatientAccount(IdentityInterface $user, User $resource): bool
    {
        if (!UserAccess::isClinician($user) || $resource->type !== 'patient') {
            return false;
        }

        $patient = FactoryLocator::get('Table')->get('Patients')
            ->find()
            ->select(['id'])
            ->where(['user_id' => $resource->id])
            ->first();

        return $patient !== null && in_array((int)$patient->id, UserAccess::getAssignedPatientIds($user), true);
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
