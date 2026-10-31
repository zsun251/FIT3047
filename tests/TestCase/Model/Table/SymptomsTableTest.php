<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SymptomsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SymptomsTable Test Case
 */
class SymptomsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SymptomsTable
     */
    protected $Symptoms;

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
        $config = $this->getTableLocator()->exists('Symptoms') ? [] : ['className' => SymptomsTable::class];
        $this->Symptoms = $this->getTableLocator()->get('Symptoms', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Symptoms);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\SymptomsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->assertEmpty($this->Symptoms->newEntity(['symptom_name' => 'Cough'])->getErrors());
        $this->assertArrayHasKey('symptom_name', $this->Symptoms->newEntity([])->getErrors());
        $this->assertArrayHasKey('symptom_name', $this->Symptoms->newEntity(['symptom_name' => str_repeat('x', 151)])->getErrors());
        $this->assertArrayHasKey('symptom_name', $this->Symptoms->newEntity(['symptom_name' => 'Wheezing'])->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\SymptomsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $entity = $this->Symptoms->newEntity(['symptom_name' => 'Wheezing'], ['validate' => false]);
        $this->assertFalse($this->Symptoms->save($entity));
        $this->assertArrayHasKey('symptom_name', $entity->getErrors());
    }
}
