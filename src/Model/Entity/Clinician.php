<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Clinician Entity
 *
 * @property int $id
 * @property int $user_id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Appointment[] $appointments
 * @property \App\Model\Entity\Resource[] $resources
 * @property \App\Model\Entity\Patient[] $patients
 */
class Clinician extends Entity
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
        'user_id' => true,
        'user' => true,
        'appointments' => true,
        'resources' => true,
        'patients' => true,
    ];
}
