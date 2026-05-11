<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * VitalsReminder Entity
 *
 * @property int $id
 * @property int $reminder_id
 * @property int $patient_id
 *
 * @property \App\Model\Entity\Reminder $reminder
 * @property \App\Model\Entity\Patient $patient
 */
class VitalsReminder extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'reminder_id' => true,
        'patient_id' => true,
        'reminder' => true,
        'patient' => true,
    ];
}
