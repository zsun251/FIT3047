<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Authentication\PasswordHasher\DefaultPasswordHasher;

/**
 * User Entity
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string|null $address
 * @property \Cake\I18n\Date|null $date_of_birth
 * @property string $type
 * @property \Cake\I18n\DateTime $created_at
 *
 * @property \App\Model\Entity\Admin $admin
 * @property \App\Model\Entity\Clinician $clinician
 * @property \App\Model\Entity\Patient $patient
 * @property string|null $reset_token
 * @property \Cake\I18n\DateTime|null $reset_token_expires
 */
class User extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'username' => true,
        'password' => true,
        'first_name' => true,
        'last_name' => true,
        'email' => true,
        'phone' => true,
        'address' => true,
        'date_of_birth' => true,
        'type' => true,
        'created_at' => true,
        'admin' => true,
        'clinician' => true,
        'patient' => true,
        'reset_token' => true,
        'reset_token_expires' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array<string>
     */
    protected array $_hidden = [
        'password',
        'reset_token'
    ];

    /**
     * Password setter - hashes password before saving
     * Returns null if password is empty (allows updates without changing password)
     *
     * @param string|null $password The password to hash
     * @return string|null The hashed password or null
     */
    protected function _setPassword(?string $password): ?string
    {
        // If password is null or empty string, return null (don't update)
        if ($password === null || trim($password) === '') {
            return null;
        }

        // Hash non-empty passwords
        return (new DefaultPasswordHasher())->hash($password);
    }
}
