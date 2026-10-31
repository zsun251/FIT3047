<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\VitalDataController Test Case
 *
 * @link \App\Controller\VitalDataController
 */
class VitalDataControllerTest extends TestCase
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
     * @link \App\Controller\VitalDataController::index()
     */
    public function testIndex(): void
    {
        $this->get('/vital-data');
        $this->assertRedirectsToLogin();

        $this->loginAs('testpatient');
        $this->get('/vital-data');
        $this->assertRedirectContains('/dashboard/patient');

        $this->loginAs('testclinician');
        $this->get('/vital-data');
        $this->assertResponseOk();
    }

    /**
     * Test view method
     *
     * @return void
     * @link \App\Controller\VitalDataController::view()
     */
    public function testView(): void
    {
        $this->loginAs('testpatient');
        $this->get('/vital-data/view/1');
        $this->assertResponseOk();

        $this->loginAs('otherpatient');
        $this->get('/vital-data/view/1');
        $this->assertRedirectContains('/dashboard/patient');

        $this->loginAs('otherclinician');
        $this->get('/vital-data/view/1');
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testclinician');
        $this->get('/vital-data/view/1');
        $this->assertResponseOk();
    }

    /**
     * Test add method
     *
     * @return void
     * @link \App\Controller\VitalDataController::add()
     */
    public function testAdd(): void
    {
        $vitals = $this->getTableLocator()->get('VitalData');
        $data = ['oxygen_saturation' => 95, 'respiratory_rate' => 16, 'blood_pressure' => '120/80', 'pulse_oximetry' => 95, 'weight' => 70];

        // A patient records data for themselves, even if the form names someone else
        $this->loginAs('testpatient');
        $this->post('/vital-data/add', ['patient_id' => 2] + $data);
        $this->assertResponseCode(302);
        $this->assertFalse($vitals->exists(['patient_id' => 2]));
        $this->assertSame(2, $vitals->find()->where(['patient_id' => 1])->count());

        // A clinician can't add data for a patient who isn't assigned to them
        $this->loginAs('otherclinician');
        $this->post('/vital-data/add', ['patient_id' => 2] + $data);
        $this->assertFalse($vitals->exists(['patient_id' => 2]));

        // Out-of-range readings are rejected
        $this->loginAs('testpatient');
        $this->post('/vital-data/add', ['oxygen_saturation' => 150] + $data);
        $this->assertResponseOk();
        $this->assertSame(2, $vitals->find()->where(['patient_id' => 1])->count());
    }

    /**
     * Test edit method
     *
     * @return void
     * @link \App\Controller\VitalDataController::edit()
     */
    public function testEdit(): void
    {
        $vitals = $this->getTableLocator()->get('VitalData');

        $this->loginAs('testpatient');
        $this->post('/vital-data/edit/1', ['weight' => 50]);
        $this->assertRedirectContains('/dashboard/patient');
        $this->assertSame(70.5, (float)$vitals->get(1)->weight);

        $this->loginAs('otherclinician');
        $this->post('/vital-data/edit/1', ['weight' => 50]);
        $this->assertRedirectContains('/dashboard/staff');
        $this->assertSame(70.5, (float)$vitals->get(1)->weight);

        $this->loginAs('testclinician');
        $this->get('/vital-data/edit/1');
        $this->assertResponseOk();
    }

    /**
     * Test delete method
     *
     * @return void
     * @link \App\Controller\VitalDataController::delete()
     */
    public function testDelete(): void
    {
        $vitals = $this->getTableLocator()->get('VitalData');

        $this->loginAs('testpatient');
        $this->post('/vital-data/delete/1');
        $this->assertRedirectContains('/dashboard/patient');
        $this->assertTrue($vitals->exists(['id' => 1]));

        $this->loginAs('testclinician');
        $this->post('/vital-data/delete/1');
        $this->assertRedirectContains('/vital-data');
        $this->assertFalse($vitals->exists(['id' => 1]));
    }
}
