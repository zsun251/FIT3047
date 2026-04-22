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
            ->notEmptyString('oxygen_saturation', 'Please enter oxygen saturation.')

            ->add('oxygen_saturation', 'numeric', [
                'rule' => 'numeric',
                'message' => 'This field must contain a valid numeric value.',
                'last' => true,
            ])

            ->add('oxygen_saturation', 'minValue', [
                'rule' => function ($value, $context) {
                    return is_numeric($value) && $value >= 0;
                },
                'message' => 'Oxygen saturation must be greater than or equal to 0.',
                'last' => true,
            ])

            ->add('oxygen_saturation', 'maxValue', [
                'rule' => function ($value, $context) {
                    return is_numeric($value) && $value <= 100;
                },
                'message' => 'Oxygen saturation cannot exceed 100.',
                'last' => true,
            ]);

        $validator
            ->notEmptyString('respiratory_rate', 'Please enter respiratory rate.')

            ->add('respiratory_rate', 'numeric', [
                'rule' => 'numeric',
                'message' => 'This field must contain a valid numeric value.',
                'last' => true,
            ])

            ->add('respiratory_rate', 'minValue', [
                'rule' => function ($value, $context) {
                    return is_numeric($value) && $value >= 0;
                },
                'message' => 'Respiratory rate must be greater than or equal to 0.',
                'last' => true,
            ])

            ->add('respiratory_rate', 'maxValue', [
                'rule' => function ($value, $context) {
                    return is_numeric($value) && $value <= 100;
                },
                'message' => 'Respiratory rate cannot exceed 100.',
                'last' => true,
            ]);

        //$validator
            //->decimal('lung_function')
            //->allowEmptyString('lung_function');

        $validator
            ->notEmptyString('blood_pressure', 'Please enter blood pressure.')

            ->add('blood_pressure', 'validFormat', [
                'rule' => function ($value, $context) {

                    // Check format first
                    if (!preg_match('/^\d{1,3}\/\d{1,3}$/', $value)) {
                        return false;
                    }
                    [$systolic, $diastolic] = explode('/', $value);

                    $systolic = (int)$systolic;
                    $diastolic = (int)$diastolic;

                    return ($systolic >= 0 && $systolic <= 300)
                        && ($diastolic >= 0 && $diastolic <= 300);
                },
                'message' => 'Blood pressure must be in the format 120/80.',
                'last' => true,
            ]);

        $validator
            ->notEmptyString('pulse_oximetry', 'Please enter pulse oximetry.')

            ->add('pulse_oximetry', 'numeric', [
                'rule' => 'numeric',
                'message' => 'This field must contain a valid numeric value.',
                'last' => true,
            ])

            ->add('pulse_oximetry', 'minValue', [
                'rule' => function ($value, $context) {
                    return is_numeric($value) && $value >= 0;
                },
                'message' => 'Pulse oximetry must be greater than or equal to 0.',
                'last' => true,
            ])

            ->add('pulse_oximetry', 'maxValue', [
                'rule' => function ($value, $context) {
                    return is_numeric($value) && $value <= 100;
                },
                'message' => 'Pulse oximetry cannot exceed 100.',
                'last' => true,
            ]);

        $validator
            ->notEmptyString('weight', 'Please enter weight.')

            ->add('weight', 'numeric', [
                'rule' => 'numeric',
                'message' => 'This field must contain a valid numeric value.',
                'last' => true,
            ])

            ->add('weight', 'minValue', [
                'rule' => function ($value, $context) {
                    return is_numeric($value) && $value >= 0;
                },
                'message' => 'Weight must be greater than or equal to 0.',
                'last' => true,
            ])

            ->add('weight', 'maxValue', [
                'rule' => function ($value, $context) {
                    return is_numeric($value) && $value <= 300;
                },
                'message' => 'Weight cannot exceed 300.',
                'last' => true,
            ]);

        $validator
            ->dateTime('recorded_at')
            ->notEmptyDateTime('recorded_at', 'Please enter the recorded date and time.');

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
