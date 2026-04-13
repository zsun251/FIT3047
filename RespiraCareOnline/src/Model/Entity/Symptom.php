<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Symptom Entity
 *
 * @property int $id
 * @property string $symptom_name
 * @property string|null $description
 *
 * @property \App\Model\Entity\VitalData[] $vital_data
 */
class Symptom extends Entity
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
        'symptom_name' => true,
        'description' => true,
        'vital_data' => true,
    ];
}
