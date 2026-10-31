<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\DashboardController Test Case
 *
 * @link \App\Controller\DashboardController
 */
class DashboardControllerTest extends TestCase
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
        'app.Symptoms',
        'app.VitalDataSymptoms',
        'app.Resources',
        'app.ContactMessages',
    ];

    /**
     * Test the staff dashboard for admins
     *
     * @return void
     */
    public function testStaff(): void
    {
        $this->get('/dashboard/staff');
        $this->assertRedirectsToLogin();

        $this->loginAs('testpatient');
        $this->get('/dashboard/staff');
        $this->assertRedirectContains('/dashboard/patient');

        $this->loginAs('testadmin');
        $this->get('/dashboard/staff');
        $this->assertResponseOk();
        $this->assertSame(1, $this->viewVariable('stats')['unread']);
        $this->assertResponseContains('casey@example.com');

        // An unknown status filter falls back to all messages
        $this->get('/dashboard/staff?status=nonsense');
        $this->assertResponseOk();
        $this->assertSame('all', $this->viewVariable('status'));
    }

    /**
     * Test filtering messages on the staff dashboard
     *
     * @return void
     */
    public function testStaffFiltersMessages(): void
    {
        $this->loginAs('testadmin');
        $this->get('/dashboard/staff?status=archived');
        $this->assertResponseOk();
        $this->assertSame(0, $this->viewVariable('recent')->count());
    }

    /**
     * Test that clinicians get stats for their own patients only
     *
     * @return void
     */
    public function testClinicianStats(): void
    {
        $this->loginAs('testclinician');
        $this->get('/dashboard/staff');
        $this->assertResponseOk();
        $stats = $this->viewVariable('stats');
        $this->assertSame(1, $stats['patients']);
        $this->assertSame(1, $stats['records']);

        $this->loginAs('otherclinician');
        $this->get('/dashboard/staff');
        $this->assertResponseOk();
        $stats = $this->viewVariable('stats');
        $this->assertSame(0, $stats['patients']);
        $this->assertSame(0, $stats['records']);
    }

    /**
     * Test the patient dashboard
     *
     * @return void
     */
    public function testPatient(): void
    {
        $this->get('/dashboard/patient');
        $this->assertRedirectsToLogin();

        $this->loginAs('testclinician');
        $this->get('/dashboard/patient');
        $this->assertResponseCode(403);

        $this->loginAs('testpatient');
        $this->get('/dashboard/patient');
        $this->assertResponseOk();
        $this->assertSame(1, $this->viewVariable('latestVital')->patient_id);
        $this->assertSame('Using an inhaler', $this->viewVariable('latestResource')->title);
    }

    /**
     * Test the patient dashboard for a patient with no data
     *
     * @return void
     */
    public function testPatientWithoutRecords(): void
    {
        $this->loginAs('otherpatient');
        $this->get('/dashboard/patient');
        $this->assertResponseOk();
        $this->assertNull($this->viewVariable('latestVital'));
    }
}
