<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CliniciansPatientsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CliniciansPatientsTable Test Case
 */
class CliniciansPatientsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CliniciansPatientsTable
     */
    protected $CliniciansPatients;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Users',
        'app.Admins',
        'app.Clinicians',
        'app.Patients',
        'app.ReminderSettings',
        'app.CliniciansPatients',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('CliniciansPatients') ? [] : ['className' => CliniciansPatientsTable::class];
        $this->CliniciansPatients = $this->getTableLocator()->get('CliniciansPatients', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->CliniciansPatients);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\CliniciansPatientsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $valid = ['clinician_id' => 2, 'patient_id' => 2, 'assigned_at' => '2026-01-01', 'status' => 'active'];
        $this->assertEmpty($this->CliniciansPatients->newEntity($valid)->getErrors());
        $this->assertArrayHasKey('assigned_at', $this->CliniciansPatients->newEntity(['assigned_at' => 'nope'] + $valid)->getErrors());
        $this->assertArrayHasKey('status', $this->CliniciansPatients->newEntity(['status' => ''] + $valid)->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\CliniciansPatientsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $valid = ['assigned_at' => '2026-01-01', 'status' => 'active'];
        $entity = $this->CliniciansPatients->newEntity(['clinician_id' => 999, 'patient_id' => 999] + $valid);
        $this->assertFalse($this->CliniciansPatients->save($entity));
        $this->assertArrayHasKey('clinician_id', $entity->getErrors());
        $this->assertArrayHasKey('patient_id', $entity->getErrors());
    }
}
