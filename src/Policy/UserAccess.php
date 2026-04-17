<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\IdentityInterface;

class UserAccess
{
    /**
     * Check that the user is staff (admin or clinician)
     */
    public static function isStaff(?IdentityInterface $user): bool
    {
        if (!$user) {
            return false;
        }

        return in_array($user->type, ['admin', 'clinician'], true);
    }

    /**
     * Check that the user is a patient
     */
    public static function isPatient(?IdentityInterface $user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->type === 'patient';
    }

    /**
     * Check admin only
     */
    public static function isAdmin(?IdentityInterface $user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->type === 'admin';
    }

    /**
     * Check clinician only
     */
    public static function isClinician(?IdentityInterface $user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->type === 'clinician';
    }



    /**
     * Determine whether the authenticated user owns a given resource.
     *
     * This method is used across policies to enforce ownership-based access control.
     *
     * It supports both:
     *  - Direct ownership (e.g. User entity where `id` matches the user)
     *  - Indirect ownership (e.g. Admin, Patient, etc. where `user_id` links back to users.id)
     *
     * Ownership resolution strategy:
     * 1. If the resource has a `user_id` field, it is treated as the owner reference.
     *    (Used for role-specific tables like admins, clinicians, patients)
     * 2. Otherwise, the resource's primary key (`id`) is assumed to represent the user.
     *    (Used for the User entity itself)
     *
     * Example mappings:
     * - User entity:        users.id === identity.id
     * - Admin entity:       admins.user_id === identity.id
     * - Patient entity:     patients.user_id === identity.id
     *
     * @param IdentityInterface $user
     *   The authenticated user identity (represents users.id via getIdentifier()).
     *
     * @param EntityInterface $resource
     *   The resource being accessed (e.g. User, Admin, Patient).
     *
     * @return bool
     *   True if the authenticated user owns the resource; false otherwise.
     */
    public static function isOwner(?IdentityInterface $user, $resource): bool
    {
        if (!$user || !$resource) {
            return false;
        }

        $userId = (int)$user->getIdentifier();
        $ownerId = $resource->get('user_id') ?? $resource->get('id');

        return $ownerId !== null && $userId === (int)$ownerId;
    }
}
