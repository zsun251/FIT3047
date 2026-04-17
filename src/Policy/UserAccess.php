<?php
namespace App\Policy;

use Authorization\IdentityInterface;
use Cake\Datasource\EntityInterface;
use Cake\Log\Log;

class UserAccess
{
    /**
     * Check that the user is of type admin or clinician
     */
    public static function isStaff(IdentityInterface $user): bool
    {
        return in_array($user->type, ['admin', 'clinician'], true);
    }

    /**
     * Check that the user is of type Patient
     */
    public static function isPatient(IdentityInterface $user): bool
    {
        return $user->type === 'patient';
    }

    /**
     * Check that the user is of type Admin
     */
    public static function isAdmin(IdentityInterface $user): bool
    {
        return $user->type === 'admin';
    }

    /**
     * Check that the user is of type clinician
     */
    public static function isClinician(IdentityInterface $user): bool
    {
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
    public static function isOwner(IdentityInterface $user, EntityInterface $resource): bool
    {
        $userId = (int)$user->getIdentifier();

        // Gets the user_id of the owner of the resource. I.e, User is associated to Admin
        $ownerId = $resource->get('user_id') ?? $resource->get('id');

        return $ownerId !== null && $userId === (int)$ownerId;
    }
}
