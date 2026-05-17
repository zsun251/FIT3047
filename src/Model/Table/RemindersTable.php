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
     * Get count of active reminders for today only
     *
     * @param int $patientId Patient ID
     * @return int Count of active reminders
     */
    public function countActive(int $patientId): int
    {
        $now = FrozenTime::now();
        $endOfDay = $now->endOfDay();

        $activeReminders = $this->getActiveReminders($patientId);

        // Filter to only today's reminders
        $todayReminders = array_filter($activeReminders, function($reminder) use ($now) {
            return $reminder->scheduled_at->format('Y-m-d') === $now->format('Y-m-d');
        });

        return count($todayReminders);
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
     * Mark a specific reminder as completed
     *
     * @param int $reminderId
     * @param int $patientId
     * @param int $vitalDataId
     * @return bool
     */
    public function completeSpecificReminder($reminderId, $patientId, $vitalDataId)
    {
        $reminder = $this->find()
            ->innerJoinWith('VitalsReminders', function (Query $q) use ($patientId) {
                return $q->where(['VitalsReminders.patient_id' => $patientId]);
            })
            ->where([
                'Reminders.id' => $reminderId,
                'Reminders.interaction_type IS' => null
            ])
            ->first();

        if (!$reminder) {
            return false;
        }

        $reminder->interaction_type = 'completed';
        $reminder->interacted_at = FrozenTime::now();
        $reminder->vital_data_id = $vitalDataId;

        return $this->save($reminder);
    }


    /**
     * Mark reminder as completed (linked to vital data submission)
     * If no specific reminder ID provided, completes the most recent active reminder
     *
     * @param int $patientId
     * @param int $vitalDataId
     * @param int|null $reminderId Optional specific reminder to complete
     * @return bool
     */
    public function completeReminderForVitalData($patientId, $vitalDataId, $reminderId = null)
    {
        // If specific reminder ID provided, complete that one
        if ($reminderId !== null) {
            return $this->completeSpecificReminder($reminderId, $patientId, $vitalDataId);
        }

        // Otherwise, find the most recent active reminder
        $now = FrozenTime::now();

        $reminder = $this->find()
            ->innerJoinWith('VitalsReminders', function (Query $q) use ($patientId) {
                return $q->where(['VitalsReminders.patient_id' => $patientId]);
            })
            ->where([
                'Reminders.scheduled_at <=' => $now,
                'Reminders.interaction_type IS' => null
            ])
            ->orderBy(['Reminders.scheduled_at' => 'DESC'])
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
