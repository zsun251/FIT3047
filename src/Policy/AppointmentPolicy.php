<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Appointment;
use Authorization\IdentityInterface;

/**
 * AppointmentPolicy
 *
 * Handles authorization for individual Appointment entities.
 * Used when authorizing actions on specific appointment records.
 *
 * Methods in this policy determine:
 * - Who can view a specific appointment
 * - Who can add new appointments
 * - Who can edit existing appointments
 * - Who can delete appointments
 */
class AppointmentPolicy
{
    /**
     * View appointment
     */
    public function canView(IdentityInterface $user, Appointment $resource): bool
    {
        return $this->hasAppointmentAccess($user, $resource);
    }

    /**
     * Add appointment (only staff can create)
     */
    public function canAdd(IdentityInterface $user, Appointment $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Edit appointment (only staff can edit)
     */
    public function canEdit(IdentityInterface $user, Appointment $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Delete appointment (only staff can delete)
     */
    public function canDelete(IdentityInterface $user, Appointment $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * List appointments
     */
    public function canIndex(IdentityInterface $user): bool
    {
        // Allow all authenticated users to see the index
        // The actual filtering happens in scopeIndex
        return UserAccess::isStaff($user) || UserAccess::isPatient($user);
    }

    /**
     * Check if user has access to this specific appointment
     */
    private function hasAppointmentAccess(IdentityInterface $user, Appointment $resource): bool
    {
        // Admins can access all appointments
        if (UserAccess::isAdmin($user)) {
            return true;
        }

        // Patients can access their own appointments
        if ($this->isAppointmentPatient($user, $resource)) {
            return true;
        }

        // Clinicians can access appointments they're assigned to
        if ($this->isAppointmentClinician($user, $resource)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user is the patient for this appointment
     */
    private function isAppointmentPatient(IdentityInterface $user, Appointment $resource): bool
    {
        $patientId = UserAccess::getPatientId($user);
        return $patientId !== null && $patientId === $resource->patient_id;
    }

    /**
     * Check if user is the clinician for this appointment
     */
    private function isAppointmentClinician(IdentityInterface $user, Appointment $resource): bool
    {
        $clinicianId = UserAccess::getClinicianId($user);
        return $clinicianId !== null && $clinicianId === $resource->clinician_id;
    }
}
