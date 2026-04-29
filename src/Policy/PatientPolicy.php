<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Patient;
use Authorization\IdentityInterface;
use Cake\Datasource\FactoryLocator;

/**
 * PatientPolicy
 *
 * Handles authorization for individual patient records.
 *
 * Access rules:
 * - Admins can manage all patients
 * - Clinicians can view/edit patients assigned to them
 * - Patients can view only their own record
 * - Only admins can create or delete patient records
 */
class PatientPolicy
{
    /**
     * View patient.
     */
    public function canView(IdentityInterface $user, Patient $resource): bool
    {
        if (UserAccess::isAdmin($user)) {
            return true;
        }

        if ($this->isOwnPatientRecord($user, $resource)) {
            return true;
        }

        return $this->canClinicianAccessPatient($user, $resource);
    }

    /**
     * Add patient (only admins can create).
     */
    public function canAdd(IdentityInterface $user, Patient $resource): bool
    {
        return UserAccess::isAdmin($user);
    }

    /**
     * Edit patient.
     */
    public function canEdit(IdentityInterface $user, Patient $resource): bool
    {
        if (UserAccess::isAdmin($user)) {
            return true;
        }

        return $this->canClinicianAccessPatient($user, $resource);
    }

    /**
     * Delete patient (only admins can delete).
     */
    public function canDelete(IdentityInterface $user, Patient $resource): bool
    {
        return UserAccess::isAdmin($user);
    }

    /**
     * Access patients index.
     */
    public function canIndex(IdentityInterface $user, Patient $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Assign patients to clinicians (only admins).
     */
    public function canAssign(IdentityInterface $user, Patient $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Check if a clinician can access this patient.
     */
    private function canClinicianAccessPatient(IdentityInterface $user, Patient $resource): bool
    {
        if (!UserAccess::isClinician($user)) {
            return false;
        }

        $clinicianId = UserAccess::getClinicianId($user);

        if ($clinicianId === null) {
            return false;
        }

        return $this->isAssignedPatientForClinician($clinicianId, (int)$resource->id);
    }

    /**
     * Check if this patient record belongs to the user as a patient.
     */
    private function isOwnPatientRecord(IdentityInterface $user, Patient $resource): bool
    {
        $patientId = UserAccess::getPatientId($user);

        return $patientId !== null && $patientId === (int)$resource->id;
    }

    /**
     * Check if the patient is currently assigned to the clinician.
     */
    private function isAssignedPatientForClinician(int $clinicianId, int $patientId): bool
    {
        if ($clinicianId <= 0 || $patientId <= 0) {
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
