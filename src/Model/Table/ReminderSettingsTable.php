<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ReminderSettingsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('reminder_settings');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

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
            ->integer('patient_id')
            ->requirePresence('patient_id', 'create')
            ->notEmptyString('patient_id');

        $validator
            ->scalar('recurrence_pattern')
            ->maxLength('recurrence_pattern', 20)
            ->requirePresence('recurrence_pattern', 'create')
            ->notEmptyString('recurrence_pattern');

        $validator
            ->time('time_slot_1')
            ->allowEmptyTime('time_slot_1');

        $validator
            ->time('time_slot_2')
            ->allowEmptyTime('time_slot_2');

        $validator
            ->time('time_slot_3')
            ->allowEmptyTime('time_slot_3');

        $validator
            ->time('time_slot_4')
            ->allowEmptyTime('time_slot_4');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

        $validator
            ->boolean('email_enabled')
            ->notEmptyString('email_enabled');

        $validator
            ->boolean('sms_enabled')
            ->notEmptyString('sms_enabled');

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
        $rules->add($rules->isUnique(['patient_id']), ['errorField' => 'patient_id']);
        $rules->add($rules->existsIn(['patient_id'], 'Patients'), ['errorField' => 'patient_id']);

        return $rules;
    }

    /**
     * Get patient's reminder settings
     *
     * @param int $patientId
     * @return \App\Model\Entity\ReminderSetting|null
     */
    public function getPatientSettings($patientId)
    {
        return $this->find()
            ->where(['patient_id' => $patientId])
            ->first();
    }

    /**
     * Update patient's time slots
     *
     * @param int $patientId
     * @param array $timeSlots
     * @return \App\Model\Entity\ReminderSetting|bool
     */
    public function updateTimeSlots($patientId, $timeSlots)
    {
        $settings = $this->getPatientSettings($patientId);

        if (!$settings) {
            // Create new settings if they don't exist
            $settings = $this->newEntity([
                'patient_id' => $patientId,
                'time_slot_1' => $timeSlots[0] ?? null,
                'time_slot_2' => $timeSlots[1] ?? null,
                'time_slot_3' => $timeSlots[2] ?? null,
                'time_slot_4' => $timeSlots[3] ?? null,
                'is_active' => true,
                'email_enabled' => true,
                'sms_enabled' => false,
                'recurrence_pattern' => 'daily',
            ]);
        } else {
            // Update existing settings
            $settings = $this->patchEntity($settings, [
                'time_slot_1' => $timeSlots[0] ?? null,
                'time_slot_2' => $timeSlots[1] ?? null,
                'time_slot_3' => $timeSlots[2] ?? null,
                'time_slot_4' => $timeSlots[3] ?? null,
            ]);
        }

        return $this->save($settings);
    }

    /**
     * Get all active patients with reminder settings
     *
     * @return \Cake\ORM\Query
     */
    public function getActivePatientsWithSettings()
    {
        return $this->find()
            ->where(['is_active' => true])
            ->contain(['Patients']);
    }
}
