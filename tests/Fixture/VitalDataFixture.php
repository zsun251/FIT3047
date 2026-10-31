<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * VitalDataFixture
 */
class VitalDataFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'patient_id' => 1,
                'oxygen_saturation' => 96.5,
                'respiratory_rate' => 16,
                'blood_pressure' => '120/80',
                'pulse_oximetry' => 96.5,
                'weight' => 70.5,
                'recorded_at' => '2026-03-21 03:31:02',
            ],
        ];
        parent::init();
    }
}
