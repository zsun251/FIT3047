<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\IdentityInterface;
use Cake\Datasource\FactoryLocator;
use Cake\ORM\Query\SelectQuery;

/**
 * AppointmentsTablePolicy
 *
 * Handles authorization query scoping for appointment collections.
 *
 * Scope rules:
 * - Admins can see all appointments
 * - Clinicians can see appointments for their currently assigned patients
 * - Patients can see only their own appointments
 */
class AppointmentsTablePolicy
{
    /**
     * Scope the staff/index query to appointments the user can access.
     *
     * @param \Authorization\IdentityInterface $user The user making the request
     * @param \Cake\ORM\Query\SelectQuery $query The base query to filter
     * @return \Cake\ORM\Query\SelectQuery The filtered query
     */
    public function scopeIndex(IdentityInterface $user, SelectQuery $query): SelectQuery
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

        if (UserAccess::isPatient($user)) {
            $patientId = UserAccess::getPatientId($user);

            if ($patientId !== null) {
                return $query->where([
                    'Appointments.patient_id' => $patientId,
                ]);
            }
        }

        return $query->where(['1 = 0']);
    }

    /**
     * Scope the patient appointments page query.
     *
     * This intentionally returns all of the patient's appointment records, not
     * only future records, because the template separates them into upcoming and
     * history sections.
     *
     * @param \Authorization\IdentityInterface $user The user making the request
     * @param \Cake\ORM\Query\SelectQuery $query The base query to filter
     * @return \Cake\ORM\Query\SelectQuery The filtered query
     */
    public function scopeUpcomingAppointments(IdentityInterface $user, SelectQuery $query): SelectQuery
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
     * Get assigned patient ids for a clinician.
     *
     * @param \Authorization\IdentityInterface $user The user making the request
     * @return int[] The patient ids assigned to the clinician
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
