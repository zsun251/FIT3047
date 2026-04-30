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
 * - Patients can list and view their own appointments
 * - Patients cannot create, edit or delete appointment records
 */
class AppointmentPolicy
{
    /**
     * View appointment.
     */
    public function canView(IdentityInterface $user, Appointment $resource): bool
    {
        if (UserAccess::isAdmin($user)) {
            return true;
        }

        if ($this->isOwnAppointment($user, $resource)) {
            return true;
        }

        return $this->canClinicianManageAppointment($user, $resource);
    }

    /**
     * Add appointment.
     *
     * Empty appointment entities are allowed for admins and clinicians so the add
     * form can load. Once submitted data has been patched onto the entity,
     * clinicians are restricted to their own assigned patients.
     */
    public function canAdd(IdentityInterface $user, Appointment $resource): bool
    {
        if (UserAccess::isAdmin($user)) {
            return true;
        }

        if (!UserAccess::isClinician($user)) {
            return false;
        }

        if (empty($resource->patient_id) && empty($resource->clinician_id)) {
            return true;
        }

        return $this->canClinicianManageAppointment($user, $resource);
    }

    /**
     * Edit appointment.
     */
    public function canEdit(IdentityInterface $user, Appointment $resource): bool
    {
        if (UserAccess::isAdmin($user)) {
            return true;
        }

        return $this->canClinicianManageAppointment($user, $resource);
    }

    /**
     * Delete appointment.
     */
    public function canDelete(IdentityInterface $user, Appointment $resource): bool
    {
        if (UserAccess::isAdmin($user)) {
            return true;
        }

        return $this->canClinicianManageAppointment($user, $resource);
    }

    /**
     * Access appointments index.
     *
     * Patients are allowed here so /appointments can authorize successfully
     * before redirecting them to /appointments/upcoming-appointments.
     */
    public function canIndex(IdentityInterface $user, Appointment $resource): bool
    {
        return UserAccess::isStaff($user) || UserAccess::isPatient($user);
    }

    /**
     * Access the patient appointments page.
     */
    public function canUpcomingAppointments(IdentityInterface $user, Appointment $resource): bool
    {
        return UserAccess::isPatient($user);
    }

    /**
     * Check if a clinician can manage this appointment.
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
     * Check if this appointment belongs to the user as a patient.
     */
    private function isOwnAppointment(IdentityInterface $user, Appointment $resource): bool
    {
        $patientId = UserAccess::getPatientId($user);

        return $patientId !== null && $patientId === (int)$resource->patient_id;
    }

    /**
     * Check if the patient is currently assigned to the clinician.
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
