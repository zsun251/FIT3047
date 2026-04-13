<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ClinicianPatientsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ClinicianPatientsTable Test Case
 */
class ClinicianPatientsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ClinicianPatientsTable
     */
    protected $ClinicianPatients;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.ClinicianPatients',
        'app.Clinicians',
        'app.Patients',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('ClinicianPatients') ? [] : ['className' => ClinicianPatientsTable::class];
        $this->ClinicianPatients = $this->getTableLocator()->get('ClinicianPatients', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ClinicianPatients);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\ClinicianPatientsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\ClinicianPatientsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
