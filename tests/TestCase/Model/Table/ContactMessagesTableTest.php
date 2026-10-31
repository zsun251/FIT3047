<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ContactMessagesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ContactMessagesTable Test Case
 */
class ContactMessagesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ContactMessagesTable
     */
    protected $ContactMessages;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.ContactMessages',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('ContactMessages') ? [] : ['className' => ContactMessagesTable::class];
        $this->ContactMessages = $this->getTableLocator()->get('ContactMessages', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ContactMessages);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\ContactMessagesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $valid = ['first_name' => 'Robin', 'last_name' => 'Lee', 'contact_number' => '0411111111', 'email' => 'robin@example.com', 'message' => 'Hello'];
        $errors = fn(array $change): array => $this->ContactMessages->newEntity($change + $valid)->getErrors();

        $this->assertEmpty($errors([]));
        $this->assertArrayHasKey('first_name', $errors(['first_name' => 'R0bin']));
        $this->assertArrayHasKey('first_name', $errors(['first_name' => ' Robin']));
        $this->assertArrayHasKey('last_name', $errors(['last_name' => 'L33']));
        $this->assertArrayHasKey('contact_number', $errors(['contact_number' => '12345']));
        $this->assertArrayHasKey('contact_number', $errors(['contact_number' => 'abcdefghij']));
        $this->assertArrayHasKey('email', $errors(['email' => 'not-an-email']));
        $this->assertArrayHasKey('message', $errors(['message' => '']));
        $this->assertArrayHasKey('message', $errors(['message' => str_repeat('x', 501)]));
        $this->assertArrayHasKey('status', $errors(['status' => 'spam']));
    }
}
