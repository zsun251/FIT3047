<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ContactMessage Entity
 *
 * @property int $id
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $contact_number
 * @property string $email
 * @property string $message
 * @property string $status
 * @property \Cake\I18n\DateTime $submitted_at
 * @property int|null $reviewed_by
 * @property \Cake\I18n\DateTime|null $reviewed_at
 */
class ContactMessage extends Entity
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
        'first_name' => true,
        'last_name' => true,
        'contact_number' => true,
        'email' => true,
        'message' => true,
        'status' => true,
        'submitted_at' => true,
        'reviewed_by' => true,
        'reviewed_at' => true,
    ];


    protected function _getFullName(): string
    {
        return trim($this->first_name . ' ' . ($this->last_name ?? ''));
    }

    protected function _getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'unread' => 'badge-unread',
            'read' => 'badge-read',
            default => 'badge-archived',
        };
    }

    protected function _getFormattedDate(): string
    {
        return $this->submitted_at?->format('d M Y') ?? '';
    }

    protected function _getFormattedTime(): string
    {
        return $this->submitted_at?->format('h:i A') ?? '';
    }

    protected function _getHasPhone(): bool
    {
        return !empty($this->contact_number);
    }

    protected function _getSearchText(): string
    {
        return strtolower(
            $this->full_name . ' ' .
            $this->email . ' ' .
            $this->message
        );
    }
}
