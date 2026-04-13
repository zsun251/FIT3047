<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Resource Entity
 *
 * @property int $id
 * @property int $clinician_id
 * @property string $title
 * @property string $type
 * @property string|null $body
 * @property string|null $url
 * @property string|null $thumbnail_url
 * @property string $status
 * @property \Cake\I18n\DateTime|null $published_at
 * @property \Cake\I18n\DateTime $created_at
 * @property \Cake\I18n\DateTime $updated_at
 *
 * @property \App\Model\Entity\Clinician $clinician
 */
class Resource extends Entity
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
        'title' => true,
        'type' => true,
        'body' => true,
        'url' => true,
        'thumbnail_url' => true,
        'status' => true,
        'published_at' => true,
        'created_at' => true,
        'updated_at' => true,
        'clinician' => true,
    ];
}
