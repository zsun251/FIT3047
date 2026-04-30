<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * VitalData Entity
 *
 * @property int $id
 * @property int $patient_id
 * @property string|null $oxygen_saturation
 * @property int|null $respiratory_rate
 * @property string|null $blood_pressure
 * @property string|null $pulse_oximetry
 * @property string|null $weight
 * @property \Cake\I18n\DateTime $recorded_at
 *
 * @property \App\Model\Entity\Patient $patient
 * @property \App\Model\Entity\Symptom[] $symptoms
 */
class VitalData extends Entity
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
        'patient_id' => true,
        'oxygen_saturation' => true,
        'respiratory_rate' => true,
        'blood_pressure' => true,
        'pulse_oximetry' => true,
        'weight' => true,
        'recorded_at' => true,
        'patient' => true,
        'symptoms' => true,
    ];
}
