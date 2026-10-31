<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\ResourcesController Test Case
 *
 * @link \App\Controller\ResourcesController
 */
class ResourcesControllerTest extends TestCase
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
        'app.Resources',
    ];

    /**
     * Test index method
     *
     * @return void
     * @link \App\Controller\ResourcesController::index()
     */
    public function testIndex(): void
    {
        $this->get('/resources');
        $this->assertRedirectsToLogin();

        $this->loginAs('testpatient');
        $this->get('/resources');
        $this->assertRedirectContains('/dashboard/patient');

        $this->loginAs('otherclinician');
        $this->get('/resources');
        $this->assertResponseOk();
        $this->assertResponseContains('Using an inhaler');
    }

    /**
     * Test view method
     *
     * @return void
     * @link \App\Controller\ResourcesController::view()
     */
    public function testView(): void
    {
        $this->loginAs('testadmin');
        $this->get('/resources/view/1');
        $this->assertResponseOk();

        $this->loginAs('testclinician');
        $this->get('/resources/view/1');
        $this->assertResponseOk();

        // A clinician can only open their own resources here
        $this->loginAs('otherclinician');
        $this->get('/resources/view/1');
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testpatient');
        $this->get('/resources/view/1');
        $this->assertRedirectContains('/dashboard/patient');
    }

    /**
     * Test add method
     *
     * @return void
     * @link \App\Controller\ResourcesController::add()
     */
    public function testAdd(): void
    {
        $resources = $this->getTableLocator()->get('Resources');
        $data = ['title' => 'Breathing basics', 'type' => 'article', 'status' => 'draft', 'body' => 'Text'];

        $this->loginAs('testpatient');
        $this->post('/resources/add', $data);
        $this->assertRedirectContains('/dashboard/patient');
        $this->assertFalse($resources->exists(['title' => 'Breathing basics']));

        // A clinician becomes the author, even if the form names someone else
        $this->loginAs('otherclinician');
        $this->post('/resources/add', $data + ['clinician_id' => 1]);
        $this->assertRedirectContains('/resources');
        $saved = $resources->find()->where(['title' => 'Breathing basics'])->firstOrFail();
        $this->assertSame(2, $saved->clinician_id);
    }

    /**
     * Test edit method
     *
     * @return void
     * @link \App\Controller\ResourcesController::edit()
     */
    public function testEdit(): void
    {
        $resources = $this->getTableLocator()->get('Resources');

        $this->loginAs('otherclinician');
        $this->post('/resources/edit/1', ['title' => 'Hacked']);
        $this->assertRedirectContains('/dashboard/staff');
        $this->assertSame('Using an inhaler', $resources->get(1)->title);

        $this->loginAs('testclinician');
        $this->get('/resources/edit/1');
        $this->assertResponseOk();
        $this->post('/resources/edit/1', ['title' => 'Inhaler guide']);
        $this->assertResponseCode(302);
        $this->assertSame('Inhaler guide', $resources->get(1)->title);
    }

    /**
     * Test delete method
     *
     * @return void
     * @link \App\Controller\ResourcesController::delete()
     */
    public function testDelete(): void
    {
        $resources = $this->getTableLocator()->get('Resources');

        $this->loginAs('otherclinician');
        $this->post('/resources/delete/1');
        $this->assertRedirectContains('/dashboard/staff');
        $this->assertTrue($resources->exists(['id' => 1]));

        $this->loginAs('testadmin');
        $this->post('/resources/delete/1');
        $this->assertRedirectContains('/resources');
        $this->assertFalse($resources->exists(['id' => 1]));
    }

    /**
     * Test the resource library for patients and staff
     *
     * @return void
     */
    public function testBrowse(): void
    {
        $resources = $this->getTableLocator()->get('Resources');
        $draft = $resources->newEntity(['clinician_id' => 1, 'title' => 'Draft guide', 'type' => 'video', 'status' => 'draft']);
        $resources->saveOrFail($draft);

        $this->get('/resources/browse');
        $this->assertRedirectsToLogin();

        // Patients only see published resources
        $this->loginAs('testpatient');
        $this->get('/resources/browse');
        $this->assertResponseOk();
        $this->assertResponseContains('Using an inhaler');
        $this->assertResponseNotContains('Draft guide');

        $this->loginAs('testadmin');
        $this->get('/resources/browse');
        $this->assertResponseContains('Draft guide');

        // Type filter, and unknown types are ignored
        $this->get('/resources/browse?type=video');
        $this->assertResponseContains('Draft guide');
        $this->assertResponseNotContains('Using an inhaler');
        $this->get('/resources/browse?type=nonsense');
        $this->assertResponseContains('Using an inhaler');
    }

    /**
     * Test reading a resource
     *
     * @return void
     */
    public function testRead(): void
    {
        $resources = $this->getTableLocator()->get('Resources');
        $draft = $resources->newEntity(['clinician_id' => 1, 'title' => 'Draft guide', 'type' => 'article', 'status' => 'draft']);
        $resources->saveOrFail($draft);

        $this->get('/resources/read/1');
        $this->assertRedirectsToLogin();

        $this->loginAs('testpatient');
        $this->get('/resources/read/1');
        $this->assertResponseOk();
        $this->assertResponseContains('How to use an inhaler.');

        // Patients can't read drafts, staff can
        $this->get('/resources/read/' . $draft->id);
        $this->assertRedirectContains('/dashboard/patient');

        $this->loginAs('otherclinician');
        $this->get('/resources/read/' . $draft->id);
        $this->assertResponseOk();
    }
}
