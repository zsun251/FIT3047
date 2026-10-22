<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Patient Entity
 *
 * @property int $id
 * @property int $user_id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Appointment[] $appointments
 * @property \App\Model\Entity\VitalData[] $vital_data
 * @property \App\Model\Entity\VitalsReminder[] $vitals_reminders
 * @property \App\Model\Entity\Clinician[] $clinicians
 */
class Patient extends Entity
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
        'vital_data' => true,
        'vitals_reminders' => true,
        'clinicians' => true,
    ];

    /**
     * Virtual field - Display name for dropdowns
     *
     * @return string
     */
    protected function _getDisplayName(): string
    {
        if (isset($this->user)) {
            return sprintf(
                '%s %s - Patient #%d',
                $this->user->first_name,
                $this->user->last_name,
                $this->id
            );
        }
        return 'Patient #' . $this->id;
    }
}
