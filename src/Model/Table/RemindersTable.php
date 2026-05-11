<?php
// src/Model/Table/RemindersTable.php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\Validation\Validator;

class RemindersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('reminders');
        $this->setDisplayField('message');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        // Associations
        $this->hasMany('VitalsReminders', [
            'foreignKey' => 'reminder_id'
        ]);

        $this->belongsTo('VitalData', [
            'foreignKey' => 'vital_data_id',
            'joinType' => 'LEFT'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->requirePresence('message', 'create')
            ->notEmptyString('message');

        $validator
            ->dateTime('scheduled_at')
            ->requirePresence('scheduled_at', 'create')
            ->notEmptyDateTime('scheduled_at');

        $validator
            ->inList('status', ['pending', 'sent'])
            ->notEmptyString('status');

        $validator
            ->inList('delivery_method', ['sms', 'email', 'system'])
            ->notEmptyString('delivery_method');

        $validator
            ->inList('interaction_type', ['dismissed', 'completed'], 'Invalid interaction type', true)
            ->allowEmptyString('interaction_type');

        return $validator;
    }

    /**
     * Get active reminders for a patient
     */
    public function getActiveReminders($patientId)
    {
        return $this->find()
            ->innerJoinWith('VitalsReminders', function (Query $q) use ($patientId) {
                return $q->where([
                    'VitalsReminders.patient_id' => $patientId,
                    'VitalsReminders.is_active' => true
                ]);
            })
            ->where([
                'Reminders.scheduled_at <=' => new \DateTime(),
                'Reminders.interaction_type IS' => null,
                'Reminders.delivery_method IN' => ['system', 'email']
            ])
            ->order(['Reminders.scheduled_at' => 'DESC'])
            ->toArray();
    }

    /**
     * Get count of pending reminders for a patient
     */
    public function getPendingCount($patientId)
    {
        return $this->find()
            ->innerJoinWith('VitalsReminders', function (Query $q) use ($patientId) {
                return $q->where([
                    'VitalsReminders.patient_id' => $patientId,
                    'VitalsReminders.is_active' => true
                ]);
            })
            ->where([
                'Reminders.scheduled_at <=' => new \DateTime(),
                'Reminders.interaction_type IS' => null
            ])
            ->count();
    }

    /**
     * Dismiss a reminder
     */
    public function dismissReminder($reminderId, $patientId)
    {
        $reminder = $this->find()
            ->innerJoinWith('VitalsReminders', function (Query $q) use ($patientId) {
                return $q->where(['VitalsReminders.patient_id' => $patientId]);
            })
            ->where(['Reminders.id' => $reminderId])
            ->first();

        if (!$reminder) {
            return false;
        }

        $reminder->interaction_type = 'dismissed';
        $reminder->interacted_at = new \DateTime();

        return $this->save($reminder);
    }

    /**
     * Mark reminder as completed (linked to vital data submission)
     */
    public function completeReminder($reminderId, $vitalDataId)
    {
        $reminder = $this->get($reminderId);

        $reminder->interaction_type = 'completed';
        $reminder->interacted_at = new \DateTime();
        $reminder->vital_data_id = $vitalDataId;

        return $this->save($reminder);
    }
}
