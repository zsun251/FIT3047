<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CliniciansTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CliniciansTable Test Case
 */
class CliniciansTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CliniciansTable
     */
    protected $Clinicians;

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
        'app.Resources',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Clinicians') ? [] : ['className' => CliniciansTable::class];
        $this->Clinicians = $this->getTableLocator()->get('Clinicians', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Clinicians);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\CliniciansTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->assertEmpty($this->Clinicians->newEntity(['user_id' => 1])->getErrors());
        $this->assertArrayHasKey('user_id', $this->Clinicians->newEntity(['user_id' => ''])->getErrors());
        $this->assertArrayHasKey('user_id', $this->Clinicians->newEntity(['user_id' => -1])->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\CliniciansTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $entity = $this->Clinicians->newEntity(['user_id' => 999]);
        $this->assertFalse($this->Clinicians->save($entity));
        $this->assertArrayHasKey('user_id', $entity->getErrors());
    }
}
