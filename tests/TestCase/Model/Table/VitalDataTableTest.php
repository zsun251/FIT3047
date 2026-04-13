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
        'app.VitalData',
        'app.Patients',
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
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\VitalDataTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
