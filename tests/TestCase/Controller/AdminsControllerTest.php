<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\AdminsController Test Case
 *
 * @link \App\Controller\AdminsController
 */
class AdminsControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use LoginAsTrait;

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
        'app.CliniciansPatients',
    ];

    /**
     * Test index method
     *
     * @return void
     * @link \App\Controller\AdminsController::index()
     */
    public function testIndex(): void
    {
        $this->get('/admins');
        $this->assertRedirectsToLogin();

        $this->loginAs('testclinician');
        $this->get('/admins');
        $this->assertRedirectContains('/dashboard/staff');
        $this->assertFlashMessage('You do not have permission to access that page.');

        $this->loginAs('testadmin');
        $this->get('/admins');
        $this->assertResponseOk();
        $this->assertResponseContains('testadmin');
    }

    /**
     * Test view method
     *
     * @return void
     * @link \App\Controller\AdminsController::view()
     */
    public function testView(): void
    {
        $this->loginAs('testpatient');
        $this->get('/admins/view/1');
        $this->assertRedirectContains('/dashboard/patient');

        $this->loginAs('testadmin');
        $this->get('/admins/view/1');
        $this->assertResponseOk();
        $this->assertResponseContains('Admin #1');
    }

    /**
     * Test add method
     *
     * @return void
     * @link \App\Controller\AdminsController::add()
     */
    public function testAdd(): void
    {
        $this->loginAs('testclinician');
        $this->get('/admins/add');
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testadmin');
        $this->get('/admins/add');
        $this->assertResponseOk();

        // The database only allows admin rows for users of type admin
        $users = $this->getTableLocator()->get('Users');
        $user = $users->newEntity([
            'username' => 'newadmin',
            'password' => 'Passw0rd!',
            'first_name' => 'New',
            'last_name' => 'Admin',
            'email' => 'newadmin@example.com',
            'type' => 'admin',
        ]);
        $users->saveOrFail($user);
        $admins = $this->getTableLocator()->get('Admins');
        $admins->deleteAll(['user_id' => $user->id]);

        $this->post('/admins/add', ['user_id' => $user->id]);
        $this->assertRedirectContains('/admins');
        $this->assertTrue($admins->exists(['user_id' => $user->id]));
    }

    /**
     * Test edit method
     *
     * @return void
     * @link \App\Controller\AdminsController::edit()
     */
    public function testEdit(): void
    {
        $this->loginAs('testclinician');
        $this->post('/admins/edit/1', ['user_id' => 1]);
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testadmin');
        $this->get('/admins/edit/1');
        $this->assertResponseOk();
        $this->assertResponseContains('Edit Admin');

        $this->post('/admins/edit/1', ['user_id' => 1]);
        $this->assertRedirectContains('/admins');
        $this->assertFlashMessage('The admin has been saved.');
    }

    /**
     * Test delete method
     *
     * @return void
     * @link \App\Controller\AdminsController::delete()
     */
    public function testDelete(): void
    {
        $admins = $this->getTableLocator()->get('Admins');

        $this->loginAs('testclinician');
        $this->post('/admins/delete/1');
        $this->assertRedirectContains('/dashboard/staff');
        $this->assertTrue($admins->exists(['id' => 1]));

        $this->loginAs('testadmin');
        $this->post('/admins/delete/1');
        $this->assertRedirectContains('/admins');
        $this->assertFalse($admins->exists(['id' => 1]));
    }
}
