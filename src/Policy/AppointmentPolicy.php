<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Appointment;
use Authorization\IdentityInterface;
use Cake\Datasource\FactoryLocator;

/**
 * AppointmentPolicy
 *
 * Handles authorization for individual appointment records.
 *
 * Access rules:
 * - Admins can manage all appointments
 * - Clinicians can manage appointments for patients assigned to them
 * - Patients can view their own appointments
 * - Patients cannot create appointment records
 */
class AppointmentPolicy
{
    /**
     * View appointment
     */
    public function canView(IdentityInterface $user, Appointment $resource): bool
    {
        if (UserAccess::isAdmin($user)) {
            return true;
        }

        if ($this->isOwnAppointment($user, $resource)) {
            return true;
        }

        if ($this->canClinicianManageAppointment($user, $resource)) {
            return true;
        }

        return false;
    }

    /**
     * Add appointment
     */
    public function canAdd(IdentityInterface $user, Appointment $resource): bool
    {
        if (UserAccess::isAdmin($user)) {
            return true;
        }

        if (!UserAccess::isClinician($user)) {
            return false;
        }

        // Allow access to the add form before appointment data is submitted
        if (empty($resource->patient_id) && empty($resource->clinician_id)) {
            return true;
        }

        return $this->canClinicianManageAppointment($user, $resource);
    }

    /**
     * Edit appointment
     */
    public function canEdit(IdentityInterface $user, Appointment $resource): bool
    {
        if (UserAccess::isAdmin($user)) {
            return true;
        }

        return $this->canClinicianManageAppointment($user, $resource);
    }

    /**
     * Delete appointment
     */
    public function canDelete(IdentityInterface $user, Appointment $resource): bool
    {
        if (UserAccess::isAdmin($user)) {
            return true;
        }

        return $this->canClinicianManageAppointment($user, $resource);
    }

    /**
     * List appointments
     */
    public function canIndex(IdentityInterface $user): bool
    {
        return UserAccess::isStaff($user) || UserAccess::isPatient($user);
    }

    /**
     * Check if clinician can manage this appointment
     */
    private function canClinicianManageAppointment(IdentityInterface $user, Appointment $resource): bool
    {
        if (!UserAccess::isClinician($user)) {
            return false;
        }

        $clinicianId = UserAccess::getClinicianId($user);
        if ($clinicianId === null) {
            return false;
        }

        if ((int)$resource->clinician_id !== $clinicianId) {
            return false;
        }

        return $this->isAssignedPatientForClinician($user, (int)$resource->patient_id);
    }

    /**
     * Check if this appointment belongs to the user as a patient
     */
    private function isOwnAppointment(IdentityInterface $user, Appointment $resource): bool
    {
        $patientId = UserAccess::getPatientId($user);

        return $patientId !== null && $patientId === (int)$resource->patient_id;
    }

    /**
     * Check if the patient is assigned to the clinician
     */
    private function isAssignedPatientForClinician(IdentityInterface $user, int $patientId): bool
    {
        $clinicianId = UserAccess::getClinicianId($user);

        if ($clinicianId === null || $patientId <= 0) {
            return false;
        }

        return FactoryLocator::get('Table')
            ->get('CliniciansPatients')
            ->exists([
                'CliniciansPatients.clinician_id' => $clinicianId,
                'CliniciansPatients.patient_id' => $patientId,
                'CliniciansPatients.unassigned_at IS' => null,
            ]);
    }
}
