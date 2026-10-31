<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\SymptomsController Test Case
 *
 * @link \App\Controller\SymptomsController
 */
class SymptomsControllerTest extends TestCase
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
        'app.VitalData',
        'app.Symptoms',
        'app.VitalDataSymptoms',
    ];

    /**
     * Test index method
     *
     * @return void
     * @link \App\Controller\SymptomsController::index()
     */
    public function testIndex(): void
    {
        $this->get('/symptoms');
        $this->assertRedirectsToLogin();

        $this->loginAs('testpatient');
        $this->get('/symptoms');
        $this->assertRedirectContains('/dashboard/patient');

        $this->loginAs('testclinician');
        $this->get('/symptoms');
        $this->assertResponseOk();
        $this->assertResponseContains('Wheezing');
    }

    /**
     * Test view method
     *
     * @return void
     * @link \App\Controller\SymptomsController::view()
     */
    public function testView(): void
    {
        $this->loginAs('testpatient');
        $this->get('/symptoms/view/1');
        $this->assertRedirectContains('/dashboard/patient');

        $this->loginAs('testadmin');
        $this->get('/symptoms/view/1');
        $this->assertResponseOk();
        $this->assertResponseContains('Whistling sound when breathing.');
    }

    /**
     * Test add method
     *
     * @return void
     * @link \App\Controller\SymptomsController::add()
     */
    public function testAdd(): void
    {
        $symptoms = $this->getTableLocator()->get('Symptoms');

        $this->loginAs('testpatient');
        $this->post('/symptoms/add', ['symptom_name' => 'Cough']);
        $this->assertRedirectContains('/dashboard/patient');
        $this->assertFalse($symptoms->exists(['symptom_name' => 'Cough']));

        $this->loginAs('testadmin');
        $this->post('/symptoms/add', ['symptom_name' => 'Cough']);
        $this->assertRedirectContains('/symptoms');
        $this->assertTrue($symptoms->exists(['symptom_name' => 'Cough']));

        // Names must be unique, and the form shows again
        $this->post('/symptoms/add', ['symptom_name' => 'Cough']);
        $this->assertResponseOk();
        $this->assertResponseContains('The symptom could not be saved.');
    }

    /**
     * Test edit method
     *
     * @return void
     * @link \App\Controller\SymptomsController::edit()
     */
    public function testEdit(): void
    {
        $symptoms = $this->getTableLocator()->get('Symptoms');

        $this->loginAs('testpatient');
        $this->post('/symptoms/edit/1', ['symptom_name' => 'Hacked']);
        $this->assertRedirectContains('/dashboard/patient');
        $this->assertSame('Wheezing', $symptoms->get(1)->symptom_name);

        $this->loginAs('testclinician');
        $this->get('/symptoms/edit/1');
        $this->assertResponseOk();
        $this->post('/symptoms/edit/1', ['symptom_name' => 'Severe wheezing']);
        $this->assertRedirectContains('/symptoms');
        $this->assertSame('Severe wheezing', $symptoms->get(1)->symptom_name);
    }

    /**
     * Test delete method
     *
     * @return void
     * @link \App\Controller\SymptomsController::delete()
     */
    public function testDelete(): void
    {
        $symptoms = $this->getTableLocator()->get('Symptoms');
        $symptoms->getConnection()->delete('vital_data_symptoms');

        $this->loginAs('testpatient');
        $this->post('/symptoms/delete/1');
        $this->assertRedirectContains('/dashboard/patient');
        $this->assertTrue($symptoms->exists(['id' => 1]));

        $this->loginAs('testadmin');
        $this->post('/symptoms/delete/1');
        $this->assertRedirectContains('/symptoms');
        $this->assertFalse($symptoms->exists(['id' => 1]));
    }
}
