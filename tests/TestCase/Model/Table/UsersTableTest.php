<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UsersTable Test Case
 */
class UsersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UsersTable
     */
    protected $Users;

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
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Users') ? [] : ['className' => UsersTable::class];
        $this->Users = $this->getTableLocator()->get('Users', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Users);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\UsersTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $valid = [
            'username' => 'newuser',
            'password' => 'Passw0rd!',
            'first_name' => 'New',
            'last_name' => 'User',
            'email' => 'newuser@example.com',
            'phone' => '0412345678',
            'address' => '1 Main Street',
            'date_of_birth' => '2000-01-01',
            'type' => 'patient',
        ];
        $errors = fn(array $change): array => $this->Users->newEntity($change + $valid)->getErrors();

        $this->assertEmpty($errors([]));
        $this->assertArrayHasKey('username', $errors(['username' => 'ab']));
        $this->assertArrayHasKey('username', $errors(['username' => 'a..b']));
        $this->assertArrayHasKey('password', $errors(['password' => 'short1A']));
        $this->assertArrayHasKey('password', $errors(['password' => 'alllowercase1']));
        $this->assertArrayHasKey('first_name', $errors(['first_name' => 'N3w']));
        $this->assertArrayHasKey('email', $errors(['email' => 'not-an-email']));
        $this->assertArrayHasKey('phone', $errors(['phone' => '0112345678']));
        $this->assertArrayHasKey('address', $errors(['address' => 'No number here']));
        $this->assertArrayHasKey('date_of_birth', $errors(['date_of_birth' => '2999-01-01']));
        $this->assertArrayHasKey('date_of_birth', $errors(['date_of_birth' => '1800-01-01']));
        $this->assertArrayHasKey('type', $errors(['type' => 'superuser']));
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\UsersTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $entity = $this->Users->newEntity([
            'username' => 'testadmin',
            'password' => 'Passw0rd!',
            'first_name' => 'New',
            'last_name' => 'User',
            'email' => 'testadmin@example.com',
            'type' => 'patient',
        ]);
        $this->assertFalse($this->Users->save($entity));
        $this->assertArrayHasKey('username', $entity->getErrors());
        $this->assertArrayHasKey('email', $entity->getErrors());
    }
}
