<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\IdentityInterface;

class UserAccess
{
    /**
     * Check if user is an admin
     */
    public static function isAdmin(IdentityInterface $user): bool
    {
        return $user->type === 'admin';
    }

    /**
     * Check if user is a clinician
     */
    public static function isClinician(IdentityInterface $user): bool
    {
        return $user->type === 'clinician';
    }

    /**
     * Check if user is a patient
     */
    public static function isPatient(IdentityInterface $user): bool
    {
        return $user->type === 'patient';
    }

    /**
     * Check if user is staff (admin or clinician)
     */
    public static function isStaff(IdentityInterface $user): bool
    {
        return self::isAdmin($user) || self::isClinician($user);
    }

    /**
     * Get the patient ID for a user (if they are a patient)
     */
    public static function getPatientId(IdentityInterface $user): ?int
    {
        // The Authorization\Identity allows direct property access to the underlying user data
        if (isset($user->patient) && $user->patient !== null) {
            return $user->patient->id;
        }
        return null;
    }

    /**
     * Get the clinician ID for a user (if they are a clinician)
     */
    public static function getClinicianId(IdentityInterface $user): ?int
    {
        if (isset($user->clinician) && $user->clinician !== null) {
            return $user->clinician->id;
        }
        return null;
    }

    /**
     * Get the admin ID for a user (if they are an admin)
     */
    public static function getAdminId(IdentityInterface $user): ?int
    {
        if (isset($user->admin) && $user->admin !== null) {
            return $user->admin->id;
        }
        return null;
    }

    /**
     * Get the user ID
     */
    public static function getUserId(IdentityInterface $user): int
    {
        return (int)$user->getIdentifier();
    }
}
