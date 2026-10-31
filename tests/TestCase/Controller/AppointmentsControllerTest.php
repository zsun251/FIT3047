<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\AppointmentsController Test Case
 *
 * @link \App\Controller\AppointmentsController
 */
class AppointmentsControllerTest extends TestCase
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
    ];

    /**
     * Test index method
     *
     * @return void
     * @link \App\Controller\AppointmentsController::index()
     */
    public function testIndex(): void
    {
        $this->get('/appointments');
        $this->assertRedirectsToLogin();

        $this->loginAs('testpatient');
        $this->get('/appointments');
        $this->assertRedirectContains('/appointments/upcoming-appointments');

        $this->loginAs('testclinician');
        $this->get('/appointments');
        $this->assertResponseOk();
        $this->assertResponseContains('Test');

        // Another clinician's appointments are hidden
        $this->loginAs('otherclinician');
        $this->get('/appointments');
        $this->assertResponseOk();
        $this->assertSame(0, $this->viewVariable('appointments')->count());
    }

    /**
     * Test view method
     *
     * @return void
     * @link \App\Controller\AppointmentsController::view()
     */
    public function testView(): void
    {
        $this->loginAs('testpatient');
        $this->get('/appointments/view/1');
        $this->assertResponseOk();

        $this->loginAs('otherpatient');
        $this->get('/appointments/view/1');
        $this->assertResponseCode(403);

        $this->loginAs('otherclinician');
        $this->get('/appointments/view/1');
        $this->assertResponseCode(403);

        $this->loginAs('testadmin');
        $this->get('/appointments/view/1');
        $this->assertResponseOk();
    }

    /**
     * Test add method
     *
     * @return void
     * @link \App\Controller\AppointmentsController::add()
     */
    public function testAdd(): void
    {
        $appointments = $this->getTableLocator()->get('Appointments');
        $data = ['patient_id' => 1, 'clinician_id' => 1, 'scheduled_at' => '2030-01-15 10:00:00', 'duration' => 30];

        $this->loginAs('testpatient');
        $this->get('/appointments/add');
        $this->assertResponseCode(403);

        // A clinician can't book for a patient who isn't assigned to them
        $this->loginAs('otherclinician');
        $this->post('/appointments/add', ['clinician_id' => 2] + $data);
        $this->assertResponseCode(403);
        $this->assertFalse($appointments->exists(['clinician_id' => 2]));

        $this->loginAs('testclinician');
        $this->post('/appointments/add', $data);
        $this->assertRedirectContains('/appointments');
        $this->assertSame(2, $appointments->find()->where(['clinician_id' => 1])->count());
    }

    /**
     * Test edit method
     *
     * @return void
     * @link \App\Controller\AppointmentsController::edit()
     */
    public function testEdit(): void
    {
        $this->loginAs('testpatient');
        $this->get('/appointments/edit/1');
        $this->assertResponseCode(403);

        $this->loginAs('otherclinician');
        $this->get('/appointments/edit/1');
        $this->assertResponseCode(403);

        $this->loginAs('testclinician');
        $this->get('/appointments/edit/1');
        $this->assertResponseOk();
    }

    /**
     * Test delete method
     *
     * @return void
     * @link \App\Controller\AppointmentsController::delete()
     */
    public function testDelete(): void
    {
        $appointments = $this->getTableLocator()->get('Appointments');

        $this->loginAs('testpatient');
        $this->post('/appointments/delete/1');
        $this->assertResponseCode(403);
        $this->assertTrue($appointments->exists(['id' => 1]));

        $this->loginAs('testclinician');
        $this->post('/appointments/delete/1');
        $this->assertRedirectContains('/appointments');
        $this->assertFalse($appointments->exists(['id' => 1]));
    }
}
