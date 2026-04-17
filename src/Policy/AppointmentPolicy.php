<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Appointment;
use Authorization\IdentityInterface;

class AppointmentPolicy
{

    /**
     * View appointment
     */
    public function canView(IdentityInterface $user, Appointment $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Add appointment
     */
    public function canAdd(IdentityInterface $user, Appointment $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Edit appointment
     */
    public function canEdit(IdentityInterface $user, Appointment $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Delete appointment
     */
    public function canDelete(IdentityInterface $user, Appointment $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Listing appointments
     */
    public function canIndex(IdentityInterface $user, Appointment $resource): bool
    {
        return UserAccess::isStaff($user);
    }
}
