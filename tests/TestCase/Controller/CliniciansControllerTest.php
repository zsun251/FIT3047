<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\CliniciansController Test Case
 *
 * @link \App\Controller\CliniciansController
 */
class CliniciansControllerTest extends TestCase
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
        'app.Appointments',
        'app.Resources',
    ];

    /**
     * Test index method
     *
     * @return void
     * @link \App\Controller\CliniciansController::index()
     */
    public function testIndex(): void
    {
        $this->get('/clinicians');
        $this->assertRedirectsToLogin();

        $this->loginAs('testclinician');
        $this->get('/clinicians');
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testadmin');
        $this->get('/clinicians');
        $this->assertResponseOk();
        $this->assertResponseContains('testclinician');
    }

    /**
     * Test view method
     *
     * @return void
     * @link \App\Controller\CliniciansController::view()
     */
    public function testView(): void
    {
        $this->loginAs('testpatient');
        $this->get('/clinicians/view/1');
        $this->assertRedirectContains('/dashboard/patient');

        // Staff can see every clinician record
        $this->loginAs('otherclinician');
        $this->get('/clinicians/view/1');
        $this->assertResponseOk();
        $this->assertResponseContains('Clinician #1');
    }

    /**
     * Test add method
     *
     * @return void
     * @link \App\Controller\CliniciansController::add()
     */
    public function testAdd(): void
    {
        $this->loginAs('testclinician');
        $this->get('/clinicians/add');
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testadmin');
        $this->get('/clinicians/add');
        $this->assertResponseOk();
    }

    /**
     * Test edit method
     *
     * @return void
     * @link \App\Controller\CliniciansController::edit()
     */
    public function testEdit(): void
    {
        $this->loginAs('testclinician');
        $this->get('/clinicians/edit/1');
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testadmin');
        $this->get('/clinicians/edit/1');
        $this->assertResponseOk();
    }

    /**
     * Test delete method
     *
     * @return void
     * @link \App\Controller\CliniciansController::delete()
     */
    public function testDelete(): void
    {
        $clinicians = $this->getTableLocator()->get('Clinicians');

        $this->loginAs('testclinician');
        $this->post('/clinicians/delete/2');
        $this->assertRedirectContains('/dashboard/staff');
        $this->assertTrue($clinicians->exists(['id' => 2]));

        $this->loginAs('testadmin');
        $this->post('/clinicians/delete/2');
        $this->assertRedirectContains('/clinicians');
        $this->assertFalse($clinicians->exists(['id' => 2]));
    }

    /**
     * Test creating a clinician record
     *
     * @return void
     */
    public function testAddSubmit(): void
    {
        $clinicians = $this->getTableLocator()->get('Clinicians');
        $users = $this->getTableLocator()->get('Users');
        $user = $users->newEntity([
            'username' => 'newclinician',
            'password' => 'Passw0rd!',
            'first_name' => 'New',
            'last_name' => 'Clinician',
            'email' => 'newclinician@example.com',
            'type' => 'clinician',
        ]);
        $users->saveOrFail($user);
        $clinicians->deleteAll(['user_id' => $user->id]);

        $this->loginAs('testclinician');
        $this->post('/clinicians/add', ['user_id' => $user->id]);
        $this->assertFalse($clinicians->exists(['user_id' => $user->id]));

        $this->loginAs('testadmin');
        $this->post('/clinicians/add', ['user_id' => $user->id]);
        $this->assertRedirectContains('/clinicians');
        $this->assertTrue($clinicians->exists(['user_id' => $user->id]));

        // A user who isn't in the system
        $this->post('/clinicians/add', ['user_id' => 999]);
        $this->assertResponseOk();
        $this->assertFalse($clinicians->exists(['user_id' => 999]));
    }

    /**
     * Test editing a clinician record
     *
     * @return void
     */
    public function testEditSubmit(): void
    {
        $this->loginAs('testclinician');
        $this->post('/clinicians/edit/2', ['user_id' => 4]);
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testadmin');
        $this->post('/clinicians/edit/2', ['user_id' => 4]);
        $this->assertRedirectContains('/clinicians');
        $this->assertFlashMessage('The clinician has been saved.');
    }

    /**
     * Test that a clinician record stays with its user
     *
     * @return void
     */
    public function testEditCannotMoveToAnotherUser(): void
    {
        $clinicians = $this->getTableLocator()->get('Clinicians');

        $this->loginAs('testadmin');
        $this->post('/clinicians/edit/2', ['user_id' => 2]);
        $this->assertResponseOk();
        $this->assertSame(4, $clinicians->get(2)->user_id);
    }
}
