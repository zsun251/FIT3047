<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * VitalDataSymptom Entity
 *
 * @property int $id
 * @property int $vital_data_id
 * @property int $symptom_id
 * @property string $severity
 * @property string|null $notes
 *
 * @property \App\Model\Entity\VitalData $vital_data
 * @property \App\Model\Entity\Symptom $symptom
 */
class VitalDataSymptom extends Entity
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
        'vital_data_id' => true,
        'symptom_id' => true,
        'severity' => true,
        'notes' => true,
        'vital_data' => true,
        'symptom' => true,
    ];
}
