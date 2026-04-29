<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Clinicians Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\AppointmentsTable&\Cake\ORM\Association\HasMany $Appointments
 * @property \App\Model\Table\ResourcesTable&\Cake\ORM\Association\HasMany $Resources
 * @property \App\Model\Table\PatientsTable&\Cake\ORM\Association\BelongsToMany $Patients
 *
 * @method \App\Model\Entity\Clinician newEmptyEntity()
 * @method \App\Model\Entity\Clinician newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Clinician> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Clinician get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Clinician findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Clinician patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Clinician> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Clinician|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Clinician saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Clinician>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Clinician>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Clinician>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Clinician> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Clinician>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Clinician>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Clinician>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Clinician> deleteManyOrFail(iterable $entities, array $options = [])
 */
class CliniciansTable extends Table
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

        $this->setTable('clinicians');
        $this->setDisplayField('display_name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Appointments', [
            'foreignKey' => 'clinician_id',
        ]);
        $this->hasMany('Resources', [
            'foreignKey' => 'clinician_id',
        ]);
        $this->belongsToMany('Patients', [
            'foreignKey' => 'clinician_id',
            'targetForeignKey' => 'patient_id',
            'joinTable' => 'clinicians_patients',
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
            ->nonNegativeInteger('user_id')
            ->notEmptyString('user_id');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
