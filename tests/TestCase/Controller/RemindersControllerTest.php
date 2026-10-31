<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\RemindersController Test Case
 *
 * @link \App\Controller\RemindersController
 */
class RemindersControllerTest extends TestCase
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
        'app.Reminders',
        'app.VitalsReminders',
    ];

    /**
     * Test the reminders page
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->get('/reminders');
        $this->assertRedirectsToLogin();

        $this->loginAs('testclinician');
        $this->get('/reminders');
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testpatient');
        $this->get('/reminders');
        $this->assertResponseOk();
        $this->assertResponseContains('Patient one reminder');
        $this->assertResponseNotContains('Patient two reminder');
    }

    /**
     * Test dismissing a reminder
     *
     * @return void
     */
    public function testDismiss(): void
    {
        $reminders = $this->getTableLocator()->get('Reminders');

        // Only by POST
        $this->loginAs('testpatient');
        $this->get('/reminders/dismiss/1');
        $this->assertResponseError();

        // Not someone else's reminder
        $this->post('/reminders/dismiss/2');
        $this->assertRedirectContains('/reminders');
        $this->assertFlashMessage('You do not have permission to dismiss this reminder.');
        $this->assertNull($reminders->get(2)->interaction_type);

        $this->post('/reminders/dismiss/1');
        $this->assertRedirectContains('/reminders');
        $this->assertFlashMessage('The reminder has been dismissed.');
        $this->assertSame('dismissed', $reminders->get(1)->interaction_type);

        // Staff can't dismiss reminders
        $this->loginAs('testadmin');
        $this->post('/reminders/dismiss/2');
        $this->assertNull($reminders->get(2)->interaction_type);
    }

    /**
     * Test reminder settings
     *
     * @return void
     */
    public function testSettings(): void
    {
        $settings = $this->getTableLocator()->get('ReminderSettings');

        $this->loginAs('testclinician');
        $this->get('/reminders/settings');
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testpatient');
        $this->get('/reminders/settings');
        $this->assertResponseOk();

        // patient_id from the form is ignored, so one patient can't change another's settings
        $this->post('/reminders/settings', ['patient_id' => 2, 'is_active' => 0]);
        $this->assertRedirectContains('/reminders');
        $this->assertFalse($settings->find()->where(['patient_id' => 1])->firstOrFail()->is_active);
        $this->assertTrue($settings->find()->where(['patient_id' => 2])->firstOrFail()->is_active);
    }

    /**
     * Test the unread reminder count endpoint
     *
     * @return void
     */
    public function testCount(): void
    {
        $this->configRequest(['headers' => ['Accept' => 'application/json']]);

        $this->loginAs('testpatient');
        $this->get('/reminders/count.json');
        $this->assertResponseOk();
        $this->assertSame(1, json_decode((string)$this->_response->getBody(), true)['count']);

        $this->post('/reminders/dismiss/1');
        $this->get('/reminders/count.json');
        $this->assertSame(0, json_decode((string)$this->_response->getBody(), true)['count']);

        $this->loginAs('testclinician');
        $this->get('/reminders/count.json');
        $this->assertRedirectContains('/dashboard/staff');
    }

    /**
     * Test the reminder dropdown endpoint
     *
     * @return void
     */
    public function testDropdown(): void
    {
        $this->configRequest(['headers' => ['Accept' => 'application/json']]);

        $this->loginAs('testpatient');
        $this->get('/reminders/dropdown.json');
        $this->assertResponseOk();
        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertTrue($body['success']);
        $this->assertSame(['Patient one reminder'], array_column($body['reminders'], 'message'));

        $this->loginAs('testadmin');
        $this->get('/reminders/dropdown.json');
        $this->assertRedirectContains('/dashboard/staff');
    }
}
