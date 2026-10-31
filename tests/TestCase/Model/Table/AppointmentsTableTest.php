<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AppointmentsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AppointmentsTable Test Case
 */
class AppointmentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AppointmentsTable
     */
    protected $Appointments;

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
        'app.Appointments',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Appointments') ? [] : ['className' => AppointmentsTable::class];
        $this->Appointments = $this->getTableLocator()->get('Appointments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Appointments);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\AppointmentsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $valid = ['patient_id' => 1, 'clinician_id' => 1, 'scheduled_at' => '2030-01-15 10:00:00', 'duration' => 30, 'status' => 'upcoming'];
        $this->assertEmpty($this->Appointments->newEntity($valid)->getErrors());
        $this->assertArrayHasKey('scheduled_at', $this->Appointments->newEntity(['patient_id' => 1, 'clinician_id' => 1, 'duration' => 30])->getErrors());
        $this->assertArrayHasKey('duration', $this->Appointments->newEntity(['duration' => ''] + $valid)->getErrors());
        $this->assertArrayHasKey('status', $this->Appointments->newEntity(['status' => str_repeat('x', 21)] + $valid)->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\AppointmentsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $entity = $this->Appointments->newEntity(['patient_id' => 999, 'clinician_id' => 999, 'scheduled_at' => '2030-01-15 10:00:00', 'duration' => 30]);
        $this->assertFalse($this->Appointments->save($entity));
        $this->assertArrayHasKey('patient_id', $entity->getErrors());
        $this->assertArrayHasKey('clinician_id', $entity->getErrors());
    }
}
