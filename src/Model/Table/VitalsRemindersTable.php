<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class VitalsRemindersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('vitals_reminders');
        $this->setPrimaryKey('id');

        $this->belongsTo('Reminders', [
            'foreignKey' => 'reminder_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('Patients', [
            'foreignKey' => 'patient_id',
            'joinType' => 'INNER'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('reminder_id')
            ->requirePresence('reminder_id', 'create')
            ->notEmptyString('reminder_id');

        $validator
            ->integer('patient_id')
            ->requirePresence('patient_id', 'create')
            ->notEmptyString('patient_id');

        return $validator;
    }
}
