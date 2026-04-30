<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\IdentityInterface;
use Cake\Datasource\FactoryLocator;
use Cake\ORM\Query\SelectQuery;

/**
 * PatientsTablePolicy
 *
 * Handles authorization query scoping for patient collections.
 *
 * Scope rules:
 * - Admins can see all patients
 * - Clinicians can see only their currently assigned patients
 * - Patients can see only their own record
 */
class PatientsTablePolicy
{
    /**
     * Scope the index query to patients the user can access.
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
            $patientIds = $this->getAssignedPatientIdsForClinician($user);

            if (empty($patientIds)) {
                return $query->where(['1 = 0']);
            }

            return $query->where([
                'Patients.id IN' => $patientIds,
            ]);
        }

        if (UserAccess::isPatient($user)) {
            $patientId = UserAccess::getPatientId($user);

            if ($patientId !== null) {
                return $query->where([
                    'Patients.id' => $patientId,
                ]);
            }
        }

        return $query->where(['1 = 0']);
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
