<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\EventInterface;
use ArrayObject;

/**
 * Users Model
 *
 * @property \App\Model\Table\AdminsTable&\Cake\ORM\Association\HasOne $Admins
 * @property \App\Model\Table\CliniciansTable&\Cake\ORM\Association\HasOne $Clinicians
 * @property \App\Model\Table\PatientsTable&\Cake\ORM\Association\HasOne $Patients
 *
 * @method \App\Model\Entity\User newEmptyEntity()
 * @method \App\Model\Entity\User newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\User> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\User findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\User> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\User>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\User>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\User>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\User> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\User>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\User>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\User>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\User> deleteManyOrFail(iterable $entities, array $options = [])
 */
class UsersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('username');
        $this->setPrimaryKey('id');

        // Explicitly define the schema for password reset columns
        $this->getSchema()->addColumn('reset_token', [
            'type' => 'string',
            'length' => 255,
            'null' => true,
        ]);
        $this->getSchema()->addColumn('reset_token_expires', [
            'type' => 'datetime',
            'null' => true,
        ]);

        // Use hasOne for one-to-one relationships
        $this->hasOne('Admins', [
            'foreignKey' => 'user_id',
            'propertyName' => 'admin'
        ]);
        $this->hasOne('Clinicians', [
            'foreignKey' => 'user_id',
            'propertyName' => 'clinician'
        ]);
        $this->hasOne('Patients', [
            'foreignKey' => 'user_id',
            'propertyName' => 'patient'
        ]);
    }    /**
     * Before marshal callback - sanitizes input data before validation
     *
     * @param \Cake\Event\EventInterface $event The event
     * @param \ArrayObject $data The data to marshal
     * @param \ArrayObject $options The options
     * @return void
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options): void
    {
        // Trim whitespace from string fields
        foreach (['username', 'first_name', 'last_name', 'email', 'address'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Sanitize phone: remove spaces, hyphens, parentheses
        if (!empty($data['phone']) && is_string($data['phone'])) {
            // Remove all non-digit characters except +
            $data['phone'] = preg_replace('/[^\d+]/', '', $data['phone']);

            // Convert +61 to 0 for Australian numbers
            if (str_starts_with($data['phone'], '+61')) {
                $data['phone'] = '0' . substr($data['phone'], 3);
            } elseif (str_starts_with($data['phone'], '61') && strlen($data['phone']) === 11) {
                $data['phone'] = '0' . substr($data['phone'], 2);
            }
        }

        // If password is empty on update, remove it from data to prevent validation
        if (!isset($options['newRecord']) || !$options['newRecord']) {
            if (isset($data['password']) && trim((string)$data['password']) === '') {
                unset($data['password']);
            }
        }
    }

    public function validationDefault(Validator $validator): Validator
    {
        // Username validation
        $validator
            ->scalar('username')
            ->maxLength('username', 50, 'Username must be 50 characters or less.')
            ->minLength('username', 3, 'Username must be at least 3 characters.')
            ->requirePresence('username', 'create')
            ->notEmptyString('username', 'Username is required.')
            ->add('username', 'validFormat', [
                'rule' => ['custom', '/^[A-Za-z0-9][A-Za-z0-9_.-]*[A-Za-z0-9]$/'],
                'message' => 'Username must start and end with a letter or number, and can only contain letters, numbers, dots, underscores, and hyphens.'
            ])
            ->add('username', 'noConsecutiveSpecial', [
                'rule' => function ($value) {
                    // Prevent consecutive special characters like ".." or "__"
                    return !preg_match('/[._-]{2,}/', $value);
                },
                'message' => 'Username cannot contain consecutive special characters.'
            ]);

        // Password validation
        $validator
            ->scalar('password')
            ->maxLength('password', 255, 'Password must be 255 characters or less.')
            ->minLength('password', 8, 'Password must be at least 8 characters.')
            ->requirePresence('password', 'create')
            ->notEmptyString('password', 'Password is required.', 'create')
            ->allowEmptyString('password', null, 'update')
            ->add('password', 'complexity', [
                'rule' => function ($value, $context) {
                    // Skip validation if password is empty on update
                    if (empty($value) && !$context['newRecord']) {
                        return true;
                    }
                    // Require at least one uppercase, one lowercase, one number
                    return preg_match('/[A-Z]/', $value) === 1
                        && preg_match('/[a-z]/', $value) === 1
                        && preg_match('/[0-9]/', $value) === 1;
                },
                'message' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.'
            ])
            ->add('password', 'noCommonPasswords', [
                'rule' => function ($value, $context) {
                    if (empty($value) && !$context['newRecord']) {
                        return true;
                    }
                    $common = ['password', 'password123', '12345678', 'qwerty', 'abc123'];
                    return !in_array(strtolower($value), $common, true);
                },
                'message' => 'Password is too common. Please choose a stronger password.'
            ]);

        // First name validation
        $validator
            ->scalar('first_name')
            ->maxLength('first_name', 100, 'First name must be 100 characters or less.')
            ->minLength('first_name', 2, 'First name must be at least 2 characters.')
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name', 'First name is required.')
            ->add('first_name', 'validFormat', [
                'rule' => ['custom', '/^[A-Za-z]+([\' -][A-Za-z]+)*$/'],
                'message' => 'First name can only contain letters, spaces, hyphens, and apostrophes, and must start with a letter.'
            ])
            ->add('first_name', 'noExcessiveSpaces', [
                'rule' => function ($value) {
                    // No double spaces
                    return preg_match('/\s{2,}/', $value) === 0;
                },
                'message' => 'First name cannot contain consecutive spaces.'
            ]);

        // Last name validation
        $validator
            ->scalar('last_name')
            ->maxLength('last_name', 100, 'Last name must be 100 characters or less.')
            ->minLength('last_name', 2, 'Last name must be at least 2 characters.')
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name', 'Last name is required.')
            ->add('last_name', 'validFormat', [
                'rule' => ['custom', '/^[A-Za-z]+([\' -][A-Za-z]+)*$/'],
                'message' => 'Last name can only contain letters, spaces, hyphens, and apostrophes, and must start with a letter.'
            ])
            ->add('last_name', 'noExcessiveSpaces', [
                'rule' => function ($value) {
                    return preg_match('/\s{2,}/', $value) === 0;
                },
                'message' => 'Last name cannot contain consecutive spaces.'
            ]);

        // Email validation
        $validator
            ->email('email', false, 'Please enter a valid email address.')
            ->requirePresence('email', 'create')
            ->notEmptyString('email', 'Email is required.')
            ->maxLength('email', 255, 'Email must be 255 characters or less.')
            ->add('email', 'validDomain', [
                'rule' => function ($value) {
                    // Additional check: ensure domain part exists
                    if (!str_contains($value, '@')) {
                        return false;
                    }
                    [$local, $domain] = explode('@', $value, 2);
                    // Check domain has at least one dot and valid TLD (more than 3 chars total)
                    return str_contains($domain, '.') && (strlen($domain) > 3);
                },
                'message' => 'Email must contain a valid domain.'
            ]);

        // Phone validation - Australian standards
        $validator
            ->scalar('phone')
            ->maxLength('phone', 15, 'Phone number must be 15 characters or less.')
            ->allowEmptyString('phone')
            ->add('phone', 'australianFormat', [
                'rule' => function ($value) {
                    if (empty($value)) {
                        return true;
                    }
                    // After beforeMarshal cleaning, phone should be 10 digits starting with 0
                    // Mobile: 04XX XXX XXX (10 digits)
                    // Landline: 0X XXXX XXXX (10 digits)
                    if (preg_match('/^0[0-9]{9}$/', $value) === 0) {
                        return false;
                    }
                    // Validate mobile numbers (must start with 04)
                    // Validate landline (02, 03, 07, 08)
                    return preg_match('/^(04|02|03|07|08)[0-9]{8}$/', $value) === 1;
                },
                'message' => 'Please enter a valid Australian phone number (mobile: 04XX XXX XXX or landline: 0X XXXX XXXX).'
            ]);

        // Address validation
        $validator
            ->scalar('address')
            ->maxLength('address', 500, 'Address must be 500 characters or less.')
            ->minLength('address', 5, 'Address must be at least 5 characters.')
            ->allowEmptyString('address')
            ->add('address', 'reasonableFormat', [
                'rule' => function ($value) {
                    if (empty($value)) {
                        return true;
                    }
                    // Must contain at least one letter and one number (street address)
                    return preg_match('/[A-Za-z]/', $value) === 1
                        && preg_match('/[0-9]/', $value) === 1;
                },
                'message' => 'Address should contain both letters and numbers.'
            ]);

        // Date of birth validation
        $validator
            ->date('date_of_birth', ['ymd'], 'Please enter a valid date.')
            ->allowEmptyDate('date_of_birth')
            ->add('date_of_birth', 'notFuture', [
                'rule' => function ($value) {
                    if (empty($value)) {
                        return true;
                    }
                    try {
                        $date = $value instanceof \Cake\I18n\Date
                            ? $value
                            : new \Cake\I18n\Date($value);
                        return $date <= new \Cake\I18n\Date();
                    } catch (\Exception $e) {
                        return false;
                    }
                },
                'message' => 'Date of birth cannot be in the future.'
            ])
            ->add('date_of_birth', 'ageRestriction', [
                'rule' => function ($value) {
                    if (empty($value)) {
                        return true;
                    }
                    try {
                        $date = $value instanceof \Cake\I18n\Date
                            ? $value
                            : new \Cake\I18n\Date($value);
                        $today = new \Cake\I18n\Date();
                        $age = $today->diff($date)->y;
                        return $age >= 0 && $age <= 150;
                    } catch (\Exception $e) {
                        return false;
                    }
                },
                'message' => 'You must enter a valid age.'
            ])
            ->add('date_of_birth', 'reasonableDate', [
                'rule' => function ($value) {
                    if (empty($value)) {
                        return true;
                    }
                    try {
                        $date = $value instanceof \Cake\I18n\Date
                            ? $value
                            : new \Cake\I18n\Date($value);
                        // Must be after 1900
                        return $date->year >= 1900;
                    } catch (\Exception $e) {
                        return false;
                    }
                },
                'message' => 'Please enter a valid date of birth (year must be 1900 or later).'
            ]);

        // Type validation
        $validator
            ->scalar('type')
            ->maxLength('type', 20)
            ->requirePresence('type', 'create')
            ->notEmptyString('type', 'User type is required.')
            ->inList('type', ['admin', 'clinician', 'patient'], 'Invalid user type. Must be admin, clinician, or patient.');

        // Created at validation
        $validator
            ->dateTime('created_at')
            ->allowEmptyDateTime('created_at');

        return $validator;
    }


    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add(
            $rules->isUnique(['username']),
            ['errorField' => 'username', 'message' => 'This username is already taken.']
        );

        $rules->add(
            $rules->isUnique(['email']),
            ['errorField' => 'email', 'message' => 'This email address is already registered.']
        );

        return $rules;
    }
}
