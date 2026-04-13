<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ContactMessages Model
 *
 * @method \App\Model\Entity\ContactMessage newEmptyEntity()
 * @method \App\Model\Entity\ContactMessage newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ContactMessage> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ContactMessage get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ContactMessage findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ContactMessage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ContactMessage> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ContactMessage|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ContactMessage saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ContactMessage>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ContactMessage>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ContactMessage>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ContactMessage> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ContactMessage>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ContactMessage>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ContactMessage>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ContactMessage> deleteManyOrFail(iterable $entities, array $options = [])
 */
class ContactMessagesTable extends Table
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

        $this->setTable('contact_messages');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('first_name')
            ->maxLength('first_name', 100, 'First name must be a maximum of 100 characters.')
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name', 'First name is required')
            ->add('first_name', 'validFormatFirst', [
                'rule' => ['custom', "/^[a-zA-Z' -]+$/"],
                'message' => 'Only letters, apostrophes, hyphens, and spaces are allowed in the first name.'
            ])
            ->add('first_name', 'trimmedFirst', [
                'rule' => fn($value) => trim($value) === $value,
                'message' => 'First name cannot start or end with spaces.'
            ])
            ->add('first_name', 'noNumbersFirst', [
                'rule' => fn($value) => !preg_match('/\d/', $value),
                'message' => 'First name cannot contain numbers.'
            ]);

        $validator
            ->scalar('last_name')
            ->maxLength('last_name', 100, 'Last name must be a maximum of 100 characters.')
            ->notEmptyString('last_name', 'Last name is required')
            ->add('last_name', 'validFormatLast', [
                'rule' => ['custom', "/^[a-zA-Z' -]+$/"],
                'message' => 'Only letters, apostrophes, hyphens, and spaces are allowed in the last name.'
            ])
            ->add('last_name', 'trimmedLast', [
                'rule' => fn($value) => trim($value) === $value,
                'message' => 'Last name cannot start or end with spaces.'
            ])
            ->add('last_name', 'noNumbersLast', [
                'rule' => fn($value) => !preg_match('/\d/', $value),
                'message' => 'Last name cannot contain numbers.'
            ]);

        $validator
            ->scalar('contact_number')
            ->minLength('contact_number', 10, 'Phone number must be a minimum of 10 digits.')
            ->maxLength('contact_number', 10, 'Phone number must be a maximum of 10 digits.')
            ->requirePresence('contact_number', 'create')
            ->notEmptyString('contact_number', 'Phone number is required.')
            ->add('contact_number', 'numeric', [
                'rule' => 'numeric',
                'message' => 'Phone number must contain digits only.'
            ]);;

        $validator
            ->requirePresence('email', 'create')
            ->notEmptyString('email', 'Email is required.')
            ->maxLength('email', 254, 'Email must not exceed 254 characters.')
            ->add('email', 'valid', [
                'rule' => 'email',
                'message' => 'Please enter a valid email address.'
            ]);

        $validator
            ->scalar('message')
            ->requirePresence('message', 'create')
            ->notEmptyString('message', 'Message cannot be empty.')
            ->maxLength('message', 500, 'Message must not exceed 500 characters.');

        $validator
            ->scalar('status')
            ->add('status', 'validValue', [
                'rule' => function ($value) {
                    return in_array($value, ['unread', 'read', 'archived']);
                },
                'message' => 'Status must be either unread, read or archived.'
            ]);

        $validator
            ->dateTime('submitted_at')
            ->notEmptyDateTime('submitted_at');

        $validator
            ->nonNegativeInteger('reviewed_by')
            ->allowEmptyString('reviewed_by');

        $validator
            ->dateTime('reviewed_at')
            ->allowEmptyDateTime('reviewed_at');

        return $validator;
    }
}
