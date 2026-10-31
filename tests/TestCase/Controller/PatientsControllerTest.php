<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\PatientsController Test Case
 *
 * @link \App\Controller\PatientsController
 */
class PatientsControllerTest extends TestCase
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
        'app.VitalData',
    ];

    /**
     * Test index method
     *
     * @return void
     * @link \App\Controller\PatientsController::index()
     */
    public function testIndex(): void
    {
        $this->get('/patients');
        $this->assertRedirectsToLogin();

        $this->loginAs('testpatient');
        $this->get('/patients');
        $this->assertRedirectContains('/dashboard/patient');

        // A clinician only sees their assigned patients
        $this->loginAs('testclinician');
        $this->get('/patients');
        $this->assertResponseOk();
        $this->assertResponseContains('testpatient');
        $this->assertResponseNotContains('otherpatient');

        $this->loginAs('testadmin');
        $this->get('/patients');
        $this->assertResponseOk();
        $this->assertResponseContains('otherpatient');
    }

    /**
     * Test view method
     *
     * @return void
     * @link \App\Controller\PatientsController::view()
     */
    public function testView(): void
    {
        // A patient sees their own record only
        $this->loginAs('testpatient');
        $this->get('/patients/view/1');
        $this->assertResponseOk();
        $this->get('/patients/view/2');
        $this->assertRedirectContains('/dashboard/patient');

        // A clinician sees assigned patients only
        $this->loginAs('testclinician');
        $this->get('/patients/view/1');
        $this->assertResponseOk();
        $this->get('/patients/view/2');
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('otherclinician');
        $this->get('/patients/view/1');
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testadmin');
        $this->get('/patients/view/2');
        $this->assertResponseOk();
    }

    /**
     * Test add method
     *
     * @return void
     * @link \App\Controller\PatientsController::add()
     */
    public function testAdd(): void
    {
        $this->loginAs('testclinician');
        $this->get('/patients/add');
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testadmin');
        $this->get('/patients/add');
        $this->assertResponseOk();
    }

    /**
     * Test edit method
     *
     * @return void
     * @link \App\Controller\PatientsController::edit()
     */
    public function testEdit(): void
    {
        $this->loginAs('testpatient');
        $this->get('/patients/edit/1');
        $this->assertRedirectContains('/dashboard/patient');

        $this->loginAs('otherclinician');
        $this->get('/patients/edit/1');
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testclinician');
        $this->get('/patients/edit/1');
        $this->assertResponseOk();
    }

    /**
     * Test delete method
     *
     * @return void
     * @link \App\Controller\PatientsController::delete()
     */
    public function testDelete(): void
    {
        $patients = $this->getTableLocator()->get('Patients');

        // Assigned clinicians can edit but not delete
        $this->loginAs('testclinician');
        $this->post('/patients/delete/1');
        $this->assertRedirectContains('/patients');
        $this->assertTrue($patients->exists(['id' => 1]));

        $this->loginAs('testadmin');
        $this->post('/patients/delete/2');
        $this->assertRedirectContains('/patients');
        $this->assertFalse($patients->exists(['id' => 2]));
    }

    /**
     * Test creating a patient record
     *
     * @return void
     */
    public function testAddSubmit(): void
    {
        $patients = $this->getTableLocator()->get('Patients');
        $users = $this->getTableLocator()->get('Users');
        $user = $users->newEntity([
            'username' => 'newpatient',
            'password' => 'Passw0rd!',
            'first_name' => 'New',
            'last_name' => 'Patient',
            'email' => 'newpatient@example.com',
            'type' => 'patient',
        ]);
        $users->saveOrFail($user);
        $patients->deleteAll(['user_id' => $user->id]);
        $this->getTableLocator()->get('ReminderSettings')->deleteAll(['patient_id NOT IN' => [1, 2]]);

        $this->loginAs('testclinician');
        $this->post('/patients/add', ['user_id' => $user->id]);
        $this->assertRedirectContains('/dashboard/staff');
        $this->assertFalse($patients->exists(['user_id' => $user->id]));

        $this->loginAs('testadmin');
        $this->post('/patients/add', ['user_id' => $user->id]);
        $this->assertRedirectContains('/patients');
        $this->assertTrue($patients->exists(['user_id' => $user->id]));
    }

    /**
     * Test editing a patient record
     *
     * @return void
     */
    public function testEditSubmit(): void
    {
        $this->loginAs('otherclinician');
        $this->post('/patients/edit/1', ['user_id' => 3]);
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testclinician');
        $this->post('/patients/edit/1', ['user_id' => 3]);
        $this->assertRedirectContains('/patients');
        $this->assertFlashMessage('The patient has been saved.');
    }

    /**
     * Test that a patient record stays with its user
     *
     * @return void
     */
    public function testEditCannotMoveToAnotherUser(): void
    {
        $patients = $this->getTableLocator()->get('Patients');

        $this->loginAs('testadmin');
        $this->post('/patients/edit/1', ['user_id' => 5]);
        $this->assertResponseOk();
        $this->assertSame(3, $patients->get(1)->user_id);
    }

    /**
     * Test opening the assignment page
     *
     * @return void
     */
    public function testAssignPage(): void
    {
        $this->get('/patients/assign');
        $this->assertRedirectsToLogin();

        $this->loginAs('testpatient');
        $this->get('/patients/assign');
        $this->assertRedirectContains('/dashboard/patient');

        $this->loginAs('testadmin');
        $this->get('/patients/assign');
        $this->assertResponseOk();

        $this->post('/patients/assign', ['clinician_id' => 2]);
        $this->assertRedirectContains('/patients/assign/2');

        $this->get('/patients/assign/2');
        $this->assertResponseOk();
        $this->assertSame([], $this->viewVariable('assignedPatientIds'));
    }

    /**
     * Test saving assignments, which changes what a clinician can open
     *
     * @return void
     */
    public function testAssignSave(): void
    {
        $this->loginAs('otherclinician');
        $this->get('/patients/view/2');
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testadmin');
        $this->post('/patients/assign/2', ['patient_ids' => [2]]);
        $this->assertRedirectContains('/patients');
        $this->assertFlashMessage('Patient assignments have been updated.');

        $this->loginAs('otherclinician');
        $this->get('/patients/view/2');
        $this->assertResponseOk();

        // Removing the patient takes the access away again
        $this->loginAs('testadmin');
        $this->post('/patients/assign/2', ['patient_ids' => []]);
        $this->assertRedirectContains('/patients');

        $this->loginAs('otherclinician');
        $this->get('/patients/view/2');
        $this->assertRedirectContains('/dashboard/staff');
    }

    /**
     * Test that a clinician can only change their own patient list
     *
     * @return void
     */
    public function testAssignLimitsClinicians(): void
    {
        $assignments = $this->getTableLocator()->get('CliniciansPatients');

        $this->loginAs('otherclinician');

        // Can't open or change a colleague's list
        $this->get('/patients/assign/1');
        $this->assertRedirectContains('/dashboard/staff');
        $this->post('/patients/assign/1', ['patient_ids' => []]);
        $this->assertRedirectContains('/dashboard/staff');
        $this->assertFlashMessage('You can only manage your own patients.');
        $this->assertTrue($assignments->exists(['clinician_id' => 1, 'patient_id' => 1]));

        // Their own list works
        $this->post('/patients/assign/2', ['patient_ids' => [2]]);
        $this->assertRedirectContains('/patients');
        $this->assertTrue($assignments->exists(['clinician_id' => 2, 'patient_id' => 2]));
    }
}
