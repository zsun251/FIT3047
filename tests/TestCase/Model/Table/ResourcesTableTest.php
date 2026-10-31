<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ResourcesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ResourcesTable Test Case
 */
class ResourcesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ResourcesTable
     */
    protected $Resources;

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
        $config = $this->getTableLocator()->exists('Resources') ? [] : ['className' => ResourcesTable::class];
        $this->Resources = $this->getTableLocator()->get('Resources', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Resources);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\ResourcesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $valid = ['clinician_id' => 1, 'title' => 'Guide', 'type' => 'article', 'status' => 'draft'];
        $this->assertEmpty($this->Resources->newEntity($valid)->getErrors());
        $this->assertArrayHasKey('title', $this->Resources->newEntity(['title' => ''] + $valid)->getErrors());
        $this->assertArrayHasKey('title', $this->Resources->newEntity(['title' => str_repeat('x', 256)] + $valid)->getErrors());
        $this->assertArrayHasKey('type', $this->Resources->newEntity(['clinician_id' => 1, 'title' => 'Guide'])->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\ResourcesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $entity = $this->Resources->newEntity(['clinician_id' => 999, 'title' => 'Guide', 'type' => 'article', 'status' => 'draft']);
        $this->assertFalse($this->Resources->save($entity));
        $this->assertArrayHasKey('clinician_id', $entity->getErrors());
    }
}
