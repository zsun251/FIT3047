<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\IdentityInterface;
use Cake\Datasource\FactoryLocator;
use Cake\ORM\Query;

/**
 * AppointmentsTablePolicy
 *
 * Handles authorization and query scoping for appointment collections.
 *
 * Scope methods in this policy:
 * - Filter which appointment records users can see in lists
 * - Apply role-based query restrictions
 * - Ensure patients only see their own appointments
 */
class AppointmentsTablePolicy
{
    /**
     * Scope the index query to show only appointments the user can access
     *
     * Filtering rules:
     * - Admins: See all appointments
     * - Clinicians: See appointments for their assigned patients
     * - Patients: See only their own appointments
     *
     * @param \Authorization\IdentityInterface $user The user making the request
     * @param \Cake\ORM\Query $query The base query to filter
     * @return \Cake\ORM\Query The filtered query
     */
    public function scopeIndex(IdentityInterface $user, Query $query)
    {
        if (UserAccess::isAdmin($user)) {
            return $query;
        }

        if (UserAccess::isClinician($user)) {
            $clinicianId = UserAccess::getClinicianId($user);
            $patientIds = $this->getAssignedPatientIdsForClinician($user);

            if ($clinicianId === null || empty($patientIds)) {
                return $query->where(['1 = 0']);
            }

            return $query->where([
                'Appointments.clinician_id' => $clinicianId,
                'Appointments.patient_id IN' => $patientIds,
            ]);
        }

        return $query->where(['1 = 0']);
    }

    /**
     * Patient appointments scope.
     * Patients only see their own appointments.
     */
    public function scopeUpcomingAppointments(IdentityInterface $user, Query $query)
    {
        if (!UserAccess::isPatient($user)) {
            return $query->where(['1 = 0']);
        }

        $patientId = UserAccess::getPatientId($user);

        if ($patientId === null) {
            return $query->where(['1 = 0']);
        }

        return $query->where([
            'Appointments.patient_id' => $patientId,
        ]);
    }

    /**
     * @return int[]
     */
    private function getAssignedPatientIdsForClinician(IdentityInterface $user): array
    {
        $clinicianId = UserAccess::getClinicianId($user);

        if ($clinicianId === null) {
            return [];
        }

        $rows = FactoryLocator::get('Table')
            ->get('CliniciansPatients')
            ->find()
            ->select(['patient_id'])
            ->where([
                'CliniciansPatients.clinician_id' => $clinicianId,
                'CliniciansPatients.unassigned_at IS' => null,
            ])
            ->all();

        $patientIds = [];
        foreach ($rows as $row) {
            $patientIds[] = (int)$row->patient_id;
        }

        $patientIds = array_values(array_unique(array_filter($patientIds)));
        sort($patientIds);

        return $patientIds;
    }
}
