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
 * @property \App\Model\Table\AdminsTable&\Cake\ORM\Association\HasMany $Admins
 * @property \App\Model\Table\CliniciansTable&\Cake\ORM\Association\HasMany $Clinicians
 * @property \App\Model\Table\PatientsTable&\Cake\ORM\Association\HasMany $Patients
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

        $this->hasMany('Admins', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Clinicians', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Patients', [
            'foreignKey' => 'user_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */

    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options): void
    {
        foreach (['username', 'first_name', 'last_name', 'email', 'phone', 'address'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        if (!empty($data['phone']) && is_string($data['phone'])) {
            $data['phone'] = preg_replace('/\s+/', '', $data['phone']);
        }
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('username')
            ->maxLength('username', 51)
            ->requirePresence('username', 'create')
            ->notEmptyString('username')
            ->add('username', 'validFormat', ['rule' => ['custom', '/^[A-Za-z0-9_.-]+$/'], 'message' => 'Username can only contain letters, numbers, dots, underscores, and hyphens.',])
            ->add('username', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->notEmptyString('password')
            ->minLength('password', 8, 'Password must be at least 8 characters long.');

        $validator
            ->scalar('first_name')
            ->maxLength('first_name', 100)
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name')
            ->add('first_name', 'validFormat', ['rule' => ['custom', '/^[A-Za-z-]+$/'], 'message' => 'First name can only contain letters and hyphens.',]);

        $validator
            ->scalar('last_name')
            ->maxLength('last_name', 100)
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name')
            ->add('last_name', 'validFormat', ['rule' => ['custom', '/^[A-Za-z-]+$/'], 'message' => 'Last name can only contain letters and hyphens.',]);

        $validator
            ->email('email', false, 'Please enter a valid email address.')
            ->requirePresence('email', 'create')
            ->notEmptyString('email')
            ->maxLength('email', 255)
            ->add('address', 'minLength', ['rule' => ['minLength', 5], 'message' => 'Address must be at least 5 characters long.',])
            ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('phone')
            ->maxLength('phone', 20)
            ->notEmptyString('phone', 'Phone number is required.')
            ->add('phone', 'validFormat', ['rule' => ['custom', '/^[0-9 ]+$/'], 'message' => 'Phone number can only contain numbers and spaces.',]);

        $validator
            ->scalar('address')
            ->allowEmptyString('address');

        $validator
            ->date('date_of_birth')
            ->allowEmptyDate('date_of_birth')
            ->add('date_of_birth', 'notFuture', [
                'rule' => function ($value) {
                    if ($value === null) {
                        return true;
                    }
                    return $value <= new \Cake\I18n\Date();
                },
                'message' => 'Invalid date of birth.',
            ]);

        $validator
            ->scalar('type')
            ->maxLength('type', 20)
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->dateTime('created_at')
            ->notEmptyDateTime('created_at');

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
        $rules->add($rules->isUnique(['username']), ['errorField' => 'username']);
        $rules->add($rules->isUnique(['email']), ['errorField' => 'email']);

        return $rules;
    }
}
