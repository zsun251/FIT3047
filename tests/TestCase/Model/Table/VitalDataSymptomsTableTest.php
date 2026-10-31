<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\VitalDataSymptomsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\VitalDataSymptomsTable Test Case
 */
class VitalDataSymptomsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\VitalDataSymptomsTable
     */
    protected $VitalDataSymptoms;

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
        'app.VitalDataSymptoms',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('VitalDataSymptoms') ? [] : ['className' => VitalDataSymptomsTable::class];
        $this->VitalDataSymptoms = $this->getTableLocator()->get('VitalDataSymptoms', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->VitalDataSymptoms);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\VitalDataSymptomsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $valid = ['vital_data_id' => 1, 'symptom_id' => 1, 'severity' => 'mild'];
        $this->assertEmpty($this->VitalDataSymptoms->newEntity($valid)->getErrors());
        $this->assertArrayHasKey('severity', $this->VitalDataSymptoms->newEntity(['vital_data_id' => 1, 'symptom_id' => 1])->getErrors());
        $this->assertArrayHasKey('severity', $this->VitalDataSymptoms->newEntity(['severity' => str_repeat('x', 11)] + $valid)->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\VitalDataSymptomsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $entity = $this->VitalDataSymptoms->newEntity(['vital_data_id' => 999, 'symptom_id' => 999, 'severity' => 'mild']);
        $this->assertFalse($this->VitalDataSymptoms->save($entity));
        $this->assertArrayHasKey('vital_data_id', $entity->getErrors());
        $this->assertArrayHasKey('symptom_id', $entity->getErrors());
    }
}
