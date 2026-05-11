<?php
// src/Model/Table/VitalsRemindersTable.php

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

        $this->addBehavior('Timestamp');

        $this->belongsTo('Reminders', [
            'foreignKey' => 'reminder_id'
        ]);

        $this->belongsTo('Patients', [
            'foreignKey' => 'patient_id'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

        $validator
            ->boolean('email_enabled')
            ->notEmptyString('email_enabled');

        return $validator;
    }

    /**
     * Get patient's reminder configuration
     */
    public function getPatientConfig($patientId)
    {
        return $this->find()
            ->where([
                'patient_id' => $patientId,
                'is_active' => true
            ])
            ->order(['created_at' => 'DESC'])
            ->first();
    }

    /**
     * Update patient's time slots
     */
    public function updateTimeSlots($patientId, $timeSlots)
    {
        $config = $this->getPatientConfig($patientId);

        if (!$config) {
            // Create new configuration
            $config = $this->newEntity([
                'patient_id' => $patientId,
                'time_slot_1' => $timeSlots[0] ?? null,
                'time_slot_2' => $timeSlots[1] ?? null,
                'time_slot_3' => $timeSlots[2] ?? null,
                'time_slot_4' => $timeSlots[3] ?? null,
                'is_active' => true,
                'email_enabled' => true
            ]);
        } else {
            // Update existing
            $config = $this->patchEntity($config, [
                'time_slot_1' => $timeSlots[0] ?? null,
                'time_slot_2' => $timeSlots[1] ?? null,
                'time_slot_3' => $timeSlots[2] ?? null,
                'time_slot_4' => $timeSlots[3] ?? null
            ]);
        }

        return $this->save($config);
    }
}
