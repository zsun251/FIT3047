<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\VitalDataTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\VitalDataTable Test Case
 */
class VitalDataTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\VitalDataTable
     */
    protected $VitalData;

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
        'app.VitalData',
        'app.Symptoms',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('VitalData') ? [] : ['className' => VitalDataTable::class];
        $this->VitalData = $this->getTableLocator()->get('VitalData', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->VitalData);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\VitalDataTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $valid = ['patient_id' => 1] + ['oxygen_saturation' => 95, 'respiratory_rate' => 16, 'blood_pressure' => '120/80', 'pulse_oximetry' => 95, 'weight' => 70];
        $this->assertEmpty($this->VitalData->newEntity($valid)->getErrors());
        $this->assertArrayHasKey('patient_id', $this->VitalData->newEntity(['patient_id' => ''] + $valid)->getErrors());
        $this->assertArrayHasKey('oxygen_saturation', $this->VitalData->newEntity(['oxygen_saturation' => 150] + $valid)->getErrors());
        $this->assertArrayHasKey('oxygen_saturation', $this->VitalData->newEntity(['oxygen_saturation' => 'abc'] + $valid)->getErrors());
        $this->assertArrayHasKey('blood_pressure', $this->VitalData->newEntity(['blood_pressure' => 'high'] + $valid)->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\VitalDataTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $entity = $this->VitalData->newEntity(['patient_id' => 999] + ['oxygen_saturation' => 95, 'respiratory_rate' => 16, 'blood_pressure' => '120/80', 'pulse_oximetry' => 95, 'weight' => 70]);
        $this->assertFalse($this->VitalData->save($entity));
        $this->assertArrayHasKey('patient_id', $entity->getErrors());
    }
}
