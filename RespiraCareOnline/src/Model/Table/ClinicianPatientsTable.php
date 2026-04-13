<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ClinicianPatients Model
 *
 * @property \App\Model\Table\CliniciansTable&\Cake\ORM\Association\BelongsTo $Clinicians
 * @property \App\Model\Table\PatientsTable&\Cake\ORM\Association\BelongsTo $Patients
 *
 * @method \App\Model\Entity\ClinicianPatient newEmptyEntity()
 * @method \App\Model\Entity\ClinicianPatient newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ClinicianPatient> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ClinicianPatient get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ClinicianPatient findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ClinicianPatient patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ClinicianPatient> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ClinicianPatient|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ClinicianPatient saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ClinicianPatient>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ClinicianPatient>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ClinicianPatient>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ClinicianPatient> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ClinicianPatient>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ClinicianPatient>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ClinicianPatient>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ClinicianPatient> deleteManyOrFail(iterable $entities, array $options = [])
 */
class ClinicianPatientsTable extends Table
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

        $this->setTable('clinician_patients');
        $this->setDisplayField('status');
        $this->setPrimaryKey('id');

        $this->belongsTo('Clinicians', [
            'foreignKey' => 'clinician_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Patients', [
            'foreignKey' => 'patient_id',
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
            ->nonNegativeInteger('clinician_id')
            ->notEmptyString('clinician_id');

        $validator
            ->nonNegativeInteger('patient_id')
            ->notEmptyString('patient_id');

        $validator
            ->date('assigned_at')
            ->notEmptyDate('assigned_at');

        $validator
            ->date('unassigned_at')
            ->allowEmptyDate('unassigned_at');

        $validator
            ->scalar('status')
            ->maxLength('status', 10)
            ->notEmptyString('status');

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
        $rules->add($rules->existsIn(['clinician_id'], 'Clinicians'), ['errorField' => 'clinician_id']);
        $rules->add($rules->existsIn(['patient_id'], 'Patients'), ['errorField' => 'patient_id']);

        return $rules;
    }
}
