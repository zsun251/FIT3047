<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\Validation\Validator;
use Cake\I18n\FrozenTime;

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
            ->inList('interaction_type', ['dismissed', 'completed'])
            ->allowEmptyString('interaction_type');

        return $validator;
    }

    /**
     * Get active reminders for a patient whose scheduled time has arrived
     *
     * @param int $patientId
     * @return array
     */
    public function getActiveReminders($patientId)
    {
        $now = FrozenTime::now();

        return $this->find()
            ->innerJoinWith('VitalsReminders', function (Query $q) use ($patientId) {
                return $q->where([
                    'VitalsReminders.patient_id' => $patientId,
                ]);
            })
            ->where([
                'Reminders.scheduled_at <=' => $now, // Only show if scheduled time has passed
                'Reminders.interaction_type IS' => null, // Not dismissed or completed
            ])
            ->order(['Reminders.scheduled_at' => 'DESC'])
            ->toArray();
    }

    /**
     * Get count of pending reminders for a patient
     *
     * @param int $patientId Patient ID
     * @return int Count of active reminders
     */
    public function getPendingCount(int $patientId): int
    {
        $now = FrozenTime::now();

        return $this->find()
            ->innerJoinWith('VitalsReminders')
            ->where([
                'VitalsReminders.patient_id' => $patientId,
                'Reminders.scheduled_at <=' => $now,
                'Reminders.interaction_type IS' => null
            ])
            ->count();
    }

    /**
     * Dismiss a reminder
     *
     * @param int $reminderId
     * @param int $patientId
     * @return bool
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
        $reminder->interacted_at = FrozenTime::now();

        return $this->save($reminder);
    }

    /**
     * Mark reminder as completed (linked to vital data submission)
     *
     * @param int $patientId
     * @param int $vitalDataId
     * @return bool
     */
    public function completeReminderForVitalData($patientId, $vitalDataId)
    {
        $now = FrozenTime::now();

        // Find the most recent active reminder for this patient
        $reminder = $this->find()
            ->innerJoinWith('VitalsReminders', function (Query $q) use ($patientId) {
                return $q->where(['VitalsReminders.patient_id' => $patientId]);
            })
            ->where([
                'Reminders.scheduled_at <=' => $now,
                'Reminders.interaction_type IS' => null
            ])
            ->order(['Reminders.scheduled_at' => 'DESC'])
            ->first();

        if (!$reminder) {
            return false;
        }

        $reminder->interaction_type = 'completed';
        $reminder->interacted_at = $now;
        $reminder->vital_data_id = $vitalDataId;

        return $this->save($reminder);
    }
}
