<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * VitalDataSymptoms Model
 *
 * @property \App\Model\Table\VitalDataTable&\Cake\ORM\Association\BelongsTo $VitalData
 * @property \App\Model\Table\SymptomsTable&\Cake\ORM\Association\BelongsTo $Symptoms
 *
 * @method \App\Model\Entity\VitalDataSymptom newEmptyEntity()
 * @method \App\Model\Entity\VitalDataSymptom newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\VitalDataSymptom> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VitalDataSymptom get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\VitalDataSymptom findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\VitalDataSymptom patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\VitalDataSymptom> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VitalDataSymptom|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\VitalDataSymptom saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\VitalDataSymptom>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VitalDataSymptom>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VitalDataSymptom>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VitalDataSymptom> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VitalDataSymptom>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VitalDataSymptom>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VitalDataSymptom>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VitalDataSymptom> deleteManyOrFail(iterable $entities, array $options = [])
 */
class VitalDataSymptomsTable extends Table
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

        $this->setTable('vital_data_symptoms');
        $this->setDisplayField('severity');
        $this->setPrimaryKey('id');

        $this->belongsTo('VitalData', [
            'foreignKey' => 'vital_data_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Symptoms', [
            'foreignKey' => 'symptom_id',
            'joinType' => 'INNER',
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
            ->nonNegativeInteger('vital_data_id')
            ->notEmptyString('vital_data_id');

        $validator
            ->nonNegativeInteger('symptom_id')
            ->notEmptyString('symptom_id');

        $validator
            ->scalar('severity')
            ->maxLength('severity', 10)
            ->requirePresence('severity', 'create')
            ->notEmptyString('severity');

        $validator
            ->scalar('notes')
            ->allowEmptyString('notes');

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
        $rules->add($rules->existsIn(['vital_data_id'], 'VitalData'), ['errorField' => 'vital_data_id']);
        $rules->add($rules->existsIn(['symptom_id'], 'Symptoms'), ['errorField' => 'symptom_id']);

        return $rules;
    }
}
