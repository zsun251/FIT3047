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
                'oxygen_saturation' => 1.5,
                'respiratory_rate' => 1,
                'blood_pressure' => 'Lorem ip',
                'pulse_oximetry' => 1.5,
                'weight' => 1.5,
                'recorded_at' => '2026-03-21 03:31:02',
            ],
        ];
        parent::init();
    }
}
