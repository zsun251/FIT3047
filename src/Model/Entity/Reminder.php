<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Reminder Entity
 *
 * @property int $id
 * @property string $message
 * @property \Cake\I18n\FrozenTime $scheduled_at
 * @property \Cake\I18n\FrozenTime|null $sent_at
 * @property \Cake\I18n\FrozenTime $created_at
 * @property string $status
 * @property string $delivery_method
 * @property \Cake\I18n\FrozenTime|null $interacted_at
 * @property string|null $interaction_type
 * @property int|null $vital_data_id
 *
 * @property \App\Model\Entity\VitalData|null $vital_data
 * @property \App\Model\Entity\VitalsReminder[] $vitals_reminders
 */
class Reminder extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'message' => true,
        'scheduled_at' => true,
        'sent_at' => true,
        'created_at' => true,
        'status' => true,
        'delivery_method' => true,
        'interacted_at' => true,
        'interaction_type' => true,
        'vital_data_id' => true,
        'vital_data' => true,
        'vitals_reminders' => true,
    ];

    /**
     * Check if reminder is active (not dismissed or completed)
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->interaction_type === null;
    }

    /**
     * Check if reminder is completed
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->interaction_type === 'completed';
    }

    /**
     * Check if reminder is dismissed
     *
     * @return bool
     */
    public function isDismissed(): bool
    {
        return $this->interaction_type === 'dismissed';
    }

    /**
     * Check if reminder is overdue
     *
     * @return bool
     */
    public function isOverdue(): bool
    {
        return $this->scheduled_at->isPast() && $this->isActive();
    }

    /**
     * Get status badge class for display
     *
     * @return string
     */
    public function getStatusBadgeClass(): string
    {
        if ($this->isCompleted()) {
            return 'success';
        }
        if ($this->isDismissed()) {
            return 'secondary';
        }
        if ($this->isOverdue()) {
            return 'danger';
        }
        return 'primary';
    }

    /**
     * Get status label for display
     *
     * @return string
     */
    public function getStatusLabel(): string
    {
        if ($this->isCompleted()) {
            return 'Completed';
        }
        if ($this->isDismissed()) {
            return 'Dismissed';
        }
        if ($this->isOverdue()) {
            return 'Overdue';
        }
        return 'Active';
    }
}
