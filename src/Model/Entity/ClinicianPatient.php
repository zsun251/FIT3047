<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ClinicianPatient Entity
 *
 * @property int $id
 * @property int $clinician_id
 * @property int $patient_id
 * @property \Cake\I18n\Date $assigned_at
 * @property \Cake\I18n\Date|null $unassigned_at
 * @property string $status
 *
 * @property \App\Model\Entity\Clinician $clinician
 * @property \App\Model\Entity\Patient $patient
 */
class ClinicianPatient extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'clinician_id' => true,
        'patient_id' => true,
        'assigned_at' => true,
        'unassigned_at' => true,
        'status' => true,
        'clinician' => true,
        'patient' => true,
    ];
}
