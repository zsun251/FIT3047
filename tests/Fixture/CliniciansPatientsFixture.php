<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CliniciansPatientsFixture
 */
class CliniciansPatientsFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'clinicians_patients';

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
                'clinician_id' => 1,
                'patient_id' => 1,
                'assigned_at' => '2026-03-21',
                'unassigned_at' => null,
                'status' => 'active',
            ],
        ];
        parent::init();
    }
}
