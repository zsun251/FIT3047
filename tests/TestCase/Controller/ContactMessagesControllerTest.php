<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\Http\TestSuite\HttpClientTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * App\Controller\ContactMessagesController Test Case
 *
 * @link \App\Controller\ContactMessagesController
 */
class ContactMessagesControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use LoginAsTrait;
    use HttpClientTrait;

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
        'app.ContactMessages',
    ];

    /**
     * Test index method
     *
     * @return void
     * @link \App\Controller\ContactMessagesController::index()
     */
    public function testIndex(): void
    {
        $this->get('/contact-messages');
        $this->assertRedirectsToLogin();

        $this->loginAs('testpatient');
        $this->get('/contact-messages');
        $this->assertRedirectContains('/dashboard/patient');

        $this->loginAs('testclinician');
        $this->get('/contact-messages');
        $this->assertResponseOk();
        $this->assertResponseContains('casey@example.com');
    }

    /**
     * Test view method
     *
     * @return void
     * @link \App\Controller\ContactMessagesController::view()
     */
    public function testView(): void
    {
        $this->loginAs('testpatient');
        $this->get('/contact-messages/view/1');
        $this->assertRedirectContains('/dashboard/patient');

        $this->loginAs('testadmin');
        $this->get('/contact-messages/view/1');
        $this->assertResponseOk();
        $this->assertResponseContains('I need help with my account.');
    }

    /**
     * Test add method
     *
     * @return void
     * @link \App\Controller\ContactMessagesController::add()
     */
    public function testAdd(): void
    {
        $messages = $this->getTableLocator()->get('ContactMessages');
        $this->enableCsrfToken();
        $this->enableRetainFlashMessages();
        $data = [
            'first_name' => 'Robin',
            'last_name' => 'Lee',
            'contact_number' => '0411111111',
            'email' => 'robin@example.com',
            'message' => 'Hello',
        ];

        // Public page, no login needed
        $this->get('/contact-messages/add');
        $this->assertResponseOk();

        // Rejected without a Turnstile token, and without calling Cloudflare
        $this->post('/contact-messages/add', $data);
        $this->assertResponseOk();
        $this->assertFlashMessage('Security verification failed. Please try again.');
        $this->assertFalse($messages->exists(['email' => 'robin@example.com']));

        // Rejected when Cloudflare says the token is invalid
        $this->mockClientPost(self::SITEVERIFY, $this->newClientResponse(200, [], '{"success":false}'));
        $this->post('/contact-messages/add', $data + ['cf_turnstile_response' => 'bad']);
        $this->assertFalse($messages->exists(['email' => 'robin@example.com']));

        // Saved when Cloudflare accepts it, with server-set status whatever the form sends
        $this->cleanupMockResponses();
        $this->mockClientPost(self::SITEVERIFY, $this->newClientResponse(200, [], '{"success":true}'));
        $this->post('/contact-messages/add', ['status' => 'archived', 'cf_turnstile_response' => 'good'] + $data);
        $this->assertRedirect('/');
        $saved = $messages->find()->where(['email' => 'robin@example.com'])->firstOrFail();
        $this->assertSame('unread', $saved->status);
    }

    /**
     * Test edit method
     *
     * @return void
     * @link \App\Controller\ContactMessagesController::edit()
     */
    public function testEdit(): void
    {
        $messages = $this->getTableLocator()->get('ContactMessages');

        $this->loginAs('testpatient');
        $this->post('/contact-messages/edit/1', ['status' => 'read']);
        $this->assertRedirectContains('/dashboard/patient');
        $this->assertSame('unread', $messages->get(1)->status);

        $this->loginAs('testadmin');
        $this->get('/contact-messages/edit/1');
        $this->assertResponseOk();
        $this->post('/contact-messages/edit/1', ['status' => 'read']);
        $this->assertRedirectContains('/contact-messages');
        $this->assertSame('read', $messages->get(1)->status);
    }

    /**
     * Test delete method
     *
     * @return void
     * @link \App\Controller\ContactMessagesController::delete()
     */
    public function testDelete(): void
    {
        $messages = $this->getTableLocator()->get('ContactMessages');

        $this->loginAs('testpatient');
        $this->post('/contact-messages/delete/1');
        $this->assertRedirectContains('/dashboard/patient');
        $this->assertTrue($messages->exists(['id' => 1]));

        $this->loginAs('testadmin');
        $this->post('/contact-messages/delete/1');
        $this->assertRedirectContains('/contact-messages');
        $this->assertFalse($messages->exists(['id' => 1]));
    }

    /**
     * Test that delete returns to a page on this site
     *
     * @return void
     */
    public function testDeleteRedirectsWithinSite(): void
    {
        $this->loginAs('testadmin');
        $this->post('/contact-messages/delete/1', ['redirect' => '/dashboard/staff?status=unread']);
        $this->assertRedirectContains('/dashboard/staff?status=unread');
    }

    /**
     * Test that delete won't redirect to another site
     *
     * @param string $redirect Submitted redirect value
     * @return void
     */
    #[DataProvider('externalRedirects')]
    public function testDeleteIgnoresExternalRedirect(string $redirect): void
    {
        $this->loginAs('testadmin');
        $this->post('/contact-messages/delete/1', ['redirect' => $redirect]);
        $this->assertRedirectContains('/contact-messages');
        $this->assertRedirectNotContains('evil.example');
    }

    /**
     * Redirect values that a browser would follow off the site
     *
     * @return array<string, array<string>>
     */
    public static function externalRedirects(): array
    {
        return [
            'absolute' => ['https://evil.example/'],
            'protocol relative' => ['//evil.example/'],
            'backslash' => ['/\\evil.example/'],
            'tab' => ["/\t/evil.example/"],
        ];
    }

    private const SITEVERIFY = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
}
