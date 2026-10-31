<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\I18n\DateTime;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\UsersController Test Case
 *
 * @link \App\Controller\UsersController
 */
class UsersControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use LoginAsTrait;
    use EmailTrait;

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
     * @link \App\Controller\UsersController::index()
     */
    public function testIndex(): void
    {
        $this->get('/users');
        $this->assertRedirectsToLogin();

        $this->loginAs('testclinician');
        $this->get('/users');
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testadmin');
        $this->get('/users');
        $this->assertResponseOk();
        $this->assertResponseContains('otherpatient');
    }

    /**
     * Test view method
     *
     * @return void
     * @link \App\Controller\UsersController::view()
     */
    public function testView(): void
    {
        // Own account only
        $this->loginAs('testpatient');
        $this->get('/users/view/3');
        $this->assertResponseOk();
        $this->get('/users/view/5');
        $this->assertRedirectContains('/dashboard/patient');

        // Assigned patients and colleagues, not other patients
        $this->loginAs('testclinician');
        $this->get('/users/view/3');
        $this->assertResponseOk();
        $this->get('/users/view/4');
        $this->assertResponseOk();
        $this->get('/users/view/5');
        $this->assertRedirectContains('/dashboard/staff');
    }

    /**
     * Test add method
     *
     * @return void
     * @link \App\Controller\UsersController::add()
     */
    public function testAdd(): void
    {
        $users = $this->getTableLocator()->get('Users');
        $data = [
            'username' => 'brandnew',
            'password' => 'Passw0rd!',
            'first_name' => 'Brand',
            'last_name' => 'New',
            'email' => 'brandnew@example.com',
            'type' => 'clinician',
        ];

        $this->loginAs('testclinician');
        $this->post('/users/add', $data);
        $this->assertRedirectContains('/dashboard/staff');
        $this->assertFalse($users->exists(['username' => 'brandnew']));

        $this->loginAs('testadmin');
        $this->post('/users/add', $data);
        $this->assertRedirectContains('/users');
        $user = $users->find()->where(['username' => 'brandnew'])->firstOrFail();
        $this->assertNotSame('Passw0rd!', $user->password);
        $this->assertTrue($this->getTableLocator()->get('Clinicians')->exists(['user_id' => $user->id]));
    }

    /**
     * Test edit method
     *
     * @return void
     * @link \App\Controller\UsersController::edit()
     */
    public function testEdit(): void
    {
        $users = $this->getTableLocator()->get('Users');

        $this->loginAs('testpatient');
        $this->post('/users/edit/5', ['first_name' => 'Hacked']);
        $this->assertRedirectContains('/dashboard/patient');
        $this->assertSame('Test', $users->get(5)->first_name);

        // Editing your own account can't change your role
        $this->post('/users/edit/3', ['first_name' => 'Renamed', 'type' => 'admin']);
        $this->assertResponseCode(302);
        $user = $users->get(3);
        $this->assertSame('Renamed', $user->first_name);
        $this->assertSame('patient', $user->type);
    }

    /**
     * Test delete method
     *
     * @return void
     * @link \App\Controller\UsersController::delete()
     */
    public function testDelete(): void
    {
        $users = $this->getTableLocator()->get('Users');

        $this->loginAs('testclinician');
        $this->post('/users/delete/5');
        $this->assertRedirectContains('/dashboard/staff');
        $this->assertTrue($users->exists(['id' => 5]));

        $this->loginAs('testadmin');
        $this->post('/users/delete/5');
        $this->assertRedirectContains('/users');
        $this->assertFalse($users->exists(['id' => 5]));
    }

    /**
     * Test logging in
     *
     * @return void
     */
    public function testLogin(): void
    {
        $this->enableCsrfToken();
        $this->enableRetainFlashMessages();

        $this->post('/users/login', ['username' => 'testpatient', 'password' => 'wrong']);
        $this->assertResponseOk();
        $this->assertFlashMessage('Invalid username or password');
        $this->assertSessionNotHasKey('Auth');

        $this->post('/users/login', ['username' => 'testpatient', 'password' => 'Passw0rd!']);
        $this->assertRedirectContains('/dashboard/patient');

        // Email works in place of the username
        $this->post('/users/login', ['username' => 'testclinician@example.com', 'password' => 'Passw0rd!']);
        $this->assertRedirectContains('/dashboard/staff');
    }

    /**
     * Test that public registration can only create patients
     *
     * @return void
     */
    public function testRegisterAlwaysCreatesPatient(): void
    {
        $this->enableCsrfToken();

        $this->post('/users/register', [
            'username' => 'selfsignup',
            'password' => 'Passw0rd!',
            'first_name' => 'Self',
            'last_name' => 'Signup',
            'email' => 'selfsignup@example.com',
            'type' => 'admin',
        ]);

        $this->assertRedirectContains('/users/login');
        $users = $this->getTableLocator()->get('Users');
        $user = $users->find()->where(['username' => 'selfsignup'])->firstOrFail();
        $this->assertSame('patient', $user->type);
    }

    /**
     * Test requesting a password reset
     *
     * @return void
     */
    public function testForgotPassword(): void
    {
        $users = $this->getTableLocator()->get('Users');
        $this->enableCsrfToken();
        $this->enableRetainFlashMessages();

        $this->get('/users/forgot-password');
        $this->assertResponseOk();

        // Unknown emails get the same answer, and no email
        $this->post('/users/forgot-password', ['email' => 'nobody@example.com']);
        $this->assertRedirectContains('/users/login');
        $this->assertFlashMessage('If that email exists, password reset instructions have been sent.');
        $this->assertNoMailSent();

        $this->post('/users/forgot-password', ['email' => 'testpatient@example.com']);
        $this->assertRedirectContains('/users/login');
        $this->assertFlashMessage('If that email exists, password reset instructions have been sent.');
        $user = $users->get(3);
        $this->assertSame(64, strlen($user->reset_token));
        $this->assertTrue($user->reset_token_expires->isFuture());
        $this->assertMailSentTo('testpatient@example.com');
        $this->assertMailContains($user->reset_token);
    }

    /**
     * Test that the development reset link only shows in debug mode
     *
     * @return void
     */
    public function testForgotPasswordHidesLinkInProduction(): void
    {
        $this->enableCsrfToken();
        $this->enableRetainFlashMessages();
        Configure::write('debug', false);

        $this->post('/users/forgot-password', ['email' => 'testpatient@example.com']);
        $this->assertRedirectContains('/users/login');
        $flash = json_encode($this->_requestSession->read('Flash'));
        $this->assertStringNotContainsString('reset-password', (string)$flash);
    }

    /**
     * Test the reset link
     *
     * @return void
     */
    public function testResetPassword(): void
    {
        $users = $this->getTableLocator()->get('Users');
        $this->enableCsrfToken();
        $this->enableRetainFlashMessages();
        $this->setResetToken('goodtoken', '+1 hour');

        $this->get('/users/reset-password/wrongtoken');
        $this->assertRedirectContains('/users/forgot-password');

        $this->get('/users/reset-password/goodtoken');
        $this->assertResponseOk();

        // Passwords must match
        $this->post('/users/reset-password/goodtoken', ['password' => 'NewPassw0rd', 'confirm_password' => 'Different1']);
        $this->assertResponseOk();
        $this->assertFlashMessage('Passwords do not match.');

        $this->post('/users/reset-password/goodtoken', ['password' => 'NewPassw0rd', 'confirm_password' => 'NewPassw0rd']);
        $this->assertRedirectContains('/users/login');
        $user = $users->get(3);
        $this->assertNull($user->reset_token);
        $this->assertNull($user->reset_token_expires);

        // The token only works once
        $this->get('/users/reset-password/goodtoken');
        $this->assertRedirectContains('/users/forgot-password');

        // Old password is gone, new one works
        $this->post('/users/login', ['username' => 'testpatient', 'password' => 'Passw0rd!']);
        $this->assertResponseOk();
        $this->post('/users/login', ['username' => 'testpatient', 'password' => 'NewPassw0rd']);
        $this->assertRedirectContains('/dashboard/patient');
    }

    /**
     * Test that an expired link is refused
     *
     * @return void
     */
    public function testResetPasswordExpiredToken(): void
    {
        $this->enableCsrfToken();
        $this->enableRetainFlashMessages();
        $this->setResetToken('oldtoken', '-1 minute');

        $this->post('/users/reset-password/oldtoken', ['password' => 'NewPassw0rd', 'confirm_password' => 'NewPassw0rd']);
        $this->assertRedirectContains('/users/forgot-password');
        $this->assertFlashMessage('Invalid or expired password reset link.');
        $this->assertSame('oldtoken', $this->getTableLocator()->get('Users')->get(3)->reset_token);
    }

    /**
     * Test that a reset follows the same password rules as registering
     *
     * @return void
     */
    public function testResetPasswordEnforcesPasswordRules(): void
    {
        $users = $this->getTableLocator()->get('Users');
        $this->enableCsrfToken();
        $this->enableRetainFlashMessages();
        $this->setResetToken('goodtoken', '+1 hour');
        $before = $users->get(3)->password;

        foreach (['a', '', 'password', 'alllowercase1'] as $weak) {
            $this->post('/users/reset-password/goodtoken', ['password' => $weak, 'confirm_password' => $weak]);
            $this->assertResponseOk();
            $user = $users->get(3);
            $this->assertSame($before, $user->password, "Password '{$weak}' should be rejected");
            $this->assertSame('goodtoken', $user->reset_token);
        }
    }

    /**
     * Test that leaving the password blank on the edit form keeps the current one
     *
     * @return void
     */
    public function testEditBlankPasswordKeepsCurrent(): void
    {
        $users = $this->getTableLocator()->get('Users');
        $before = $users->get(3)->password;

        $this->loginAs('testpatient');
        $this->post('/users/edit/3', ['first_name' => 'Renamed', 'password' => '']);
        $this->assertResponseCode(302);
        $this->assertSame($before, $users->get(3)->password);
    }

    /**
     * Give testpatient a reset token.
     *
     * @param string $token Token
     * @param string $expires Relative expiry time
     * @return void
     */
    private function setResetToken(string $token, string $expires): void
    {
        $this->getTableLocator()->get('Users')->updateAll(
            ['reset_token' => $token, 'reset_token_expires' => new DateTime($expires)],
            ['id' => 3],
        );
    }
}
