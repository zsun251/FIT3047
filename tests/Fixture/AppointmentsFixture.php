<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AppointmentsFixture
 */
class AppointmentsFixture extends TestFixture
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
                'clinician_id' => 1,
                'scheduled_at' => '2026-03-21 03:30:54',
                'duration' => 1,
                'status' => 'Lorem ipsum dolor ',
            ],
        ];
        parent::init();
    }
}
