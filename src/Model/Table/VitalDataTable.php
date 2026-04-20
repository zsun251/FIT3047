<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * VitalData Model
 *
 * @property \App\Model\Table\PatientsTable&\Cake\ORM\Association\BelongsTo $Patients
 * @property \App\Model\Table\SymptomsTable&\Cake\ORM\Association\BelongsToMany $Symptoms
 *
 * @method \App\Model\Entity\VitalData newEmptyEntity()
 * @method \App\Model\Entity\VitalData newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\VitalData> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VitalData get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\VitalData findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\VitalData patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\VitalData> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VitalData|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\VitalData saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\VitalData>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VitalData>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VitalData>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VitalData> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VitalData>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VitalData>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VitalData>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VitalData> deleteManyOrFail(iterable $entities, array $options = [])
 */
class VitalDataTable extends Table
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

        $this->setTable('vital_data');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Patients', [
            'foreignKey' => 'patient_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsToMany('Symptoms', [
            'foreignKey' => 'vital_data_id',
            'targetForeignKey' => 'symptom_id',
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
            ->nonNegativeInteger('patient_id', 'Please select a patient.')
            ->notEmptyString('patient_id', 'Please select a patient.');

        $validator
            ->decimal('oxygen_saturation', 2, 'Oxygen saturation must be a number.')
            ->greaterThanOrEqual('oxygen_saturation', 50, 'Oxygen saturation must be at least 50.')
            ->lessThanOrEqual('oxygen_saturation', 100, 'Oxygen saturation cannot be more than 100.')
            ->notEmptyString('oxygen_saturation', 'Please enter oxygen saturation.');

        $validator
            ->allowEmptyString('respiratory_rate');

        $validator
            ->decimal('lung_function')
            ->allowEmptyString('lung_function');

        $validator
            ->scalar('blood_pressure')
            ->maxLength('blood_pressure', 10)
            ->allowEmptyString('blood_pressure');

        $validator
            ->decimal('pulse_oximetry')
            ->allowEmptyString('pulse_oximetry');

        $validator
            ->decimal('weight')
            ->allowEmptyString('weight');

        $validator
            ->dateTime('recorded_at')
            ->notEmptyDateTime('recorded_at');

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
        $rules->add($rules->existsIn(['patient_id'], 'Patients'), ['errorField' => 'patient_id']);

        return $rules;
    }
}
