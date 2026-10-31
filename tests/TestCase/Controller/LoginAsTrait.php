<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

/**
 * Logs a fixture user in for integration tests.
 *
 * UsersFixture provides testadmin, testclinician (clinician 1, assigned to
 * patient 1), testpatient (patient 1), otherclinician (clinician 2, no patients)
 * and otherpatient (patient 2).
 */
trait LoginAsTrait
{
    /**
     * Start a session as the given fixture user.
     *
     * @param string $username Fixture username
     * @return void
     */
    protected function loginAs(string $username): void
    {
        $user = $this->getTableLocator()->get('Users')->find()->where(['username' => $username])->firstOrFail();
        $this->session(['Auth' => $user]);
        $this->enableCsrfToken();
        $this->enableRetainFlashMessages();
    }

    /**
     * Assert the request was turned away and sent to the login page.
     *
     * @return void
     */
    protected function assertRedirectsToLogin(): void
    {
        $this->assertRedirectContains('/users/login');
    }
}
