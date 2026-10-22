<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ReminderSetting Entity
 *
 * @property int $id
 * @property int $patient_id
 * @property string $recurrence_pattern
 * @property \Cake\I18n\Time|null $time_slot_1
 * @property \Cake\I18n\Time|null $time_slot_2
 * @property \Cake\I18n\Time|null $time_slot_3
 * @property \Cake\I18n\Time|null $time_slot_4
 * @property bool $is_active
 * @property bool $email_enabled
 * @property bool $sms_enabled
 * @property \Cake\I18n\DateTime $created_at
 * @property \Cake\I18n\DateTime $updated_at
 *
 * @property \App\Model\Entity\Patient $patient
 */
class ReminderSetting extends Entity
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
        'recurrence_pattern' => true,
        'time_slot_1' => true,
        'time_slot_2' => true,
        'time_slot_3' => true,
        'time_slot_4' => true,
        'is_active' => true,
        'email_enabled' => true,
        'sms_enabled' => true,
        'created_at' => true,
        'updated_at' => true,
        'patient' => true,
    ];

    /**
     * Get all configured time slots as an array
     *
     * @return array
     */
    public function getTimeSlots(): array
    {
        $slots = [];

        if ($this->time_slot_1) {
            $slots[] = $this->time_slot_1;
        }
        if ($this->time_slot_2) {
            $slots[] = $this->time_slot_2;
        }
        if ($this->time_slot_3) {
            $slots[] = $this->time_slot_3;
        }
        if ($this->time_slot_4) {
            $slots[] = $this->time_slot_4;
        }

        return $slots;
    }

    /**
     * Get count of configured time slots
     *
     * @return int
     */
    public function getTimeSlotCount(): int
    {
        return count($this->getTimeSlots());
    }

    /**
     * Check if any time slots are configured
     *
     * @return bool
     */
    public function hasTimeSlots(): bool
    {
        return $this->getTimeSlotCount() > 0;
    }

    /**
     * Get formatted time slots for display
     *
     * @return array
     */
    public function getFormattedTimeSlots(): array
    {
        $formatted = [];

        foreach ($this->getTimeSlots() as $slot) {
            if ($slot instanceof \Cake\I18n\FrozenTime) {
                $formatted[] = $slot->format('g:i A');
            } elseif (is_string($slot)) {
                $formatted[] = date('g:i A', strtotime($slot));
            }
        }

        return $formatted;
    }

    /**
     * Check if email reminders should be sent
     *
     * @return bool
     */
    public function shouldSendEmail(): bool
    {
        return $this->is_active && $this->email_enabled;
    }

    /**
     * Check if SMS reminders should be sent
     *
     * @return bool
     */
    public function shouldSendSms(): bool
    {
        return $this->is_active && $this->sms_enabled;
    }
}
