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
        'app.VitalDataSymptoms',
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
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\VitalDataSymptomsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
