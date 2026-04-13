<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Symptoms Model
 *
 * @property \App\Model\Table\VitalDataTable&\Cake\ORM\Association\BelongsToMany $VitalData
 *
 * @method \App\Model\Entity\Symptom newEmptyEntity()
 * @method \App\Model\Entity\Symptom newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Symptom> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Symptom get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Symptom findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Symptom patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Symptom> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Symptom|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Symptom saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Symptom>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Symptom>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Symptom>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Symptom> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Symptom>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Symptom>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Symptom>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Symptom> deleteManyOrFail(iterable $entities, array $options = [])
 */
class SymptomsTable extends Table
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

        $this->setTable('symptoms');
        $this->setDisplayField('symptom_name');
        $this->setPrimaryKey('id');

        $this->belongsToMany('VitalData', [
            'foreignKey' => 'symptom_id',
            'targetForeignKey' => 'vital_data_id',
            'joinTable' => 'vital_data_symptoms',
        ]);
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
            ->scalar('symptom_name')
            ->maxLength('symptom_name', 150)
            ->requirePresence('symptom_name', 'create')
            ->notEmptyString('symptom_name')
            ->add('symptom_name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

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
        $rules->add($rules->isUnique(['symptom_name']), ['errorField' => 'symptom_name']);

        return $rules;
    }
}
