<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use ContentBlocks\Model\Entity\ContentBlock;
use Laminas\Diactoros\UploadedFile;

/**
 * App\Controller\ContentBlocksController Test Case
 *
 * @link \App\Controller\ContentBlocksController
 */
class ContentBlocksControllerTest extends TestCase
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
    ];

    /**
     * Content block changed by the current test, restored afterwards
     *
     * @var array<string, string|null>
     */
    private array $original = [];

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        // content_blocks is seeded once per run, not reset by a fixture
        $blocks = $this->getTableLocator()->get('ContentBlocks.ContentBlocks');
        foreach ($this->original as $slug => $value) {
            $blocks->updateAll(['value' => $value, 'previous_value' => null], ['slug' => $slug]);
        }

        parent::tearDown();
    }

    /**
     * Get a seeded block and remember its value for tearDown.
     *
     * @param string $slug Block slug
     * @return \ContentBlocks\Model\Entity\ContentBlock
     */
    private function block(string $slug): ContentBlock
    {
        $block = $this->getTableLocator()->get('ContentBlocks.ContentBlocks')
            ->find()->where(['slug' => $slug])->firstOrFail();
        $this->original[$slug] ??= $block->value;

        return $block;
    }

    /**
     * Read a block's current value.
     *
     * @param string $slug Block slug
     * @return string|null
     */
    private function value(string $slug): ?string
    {
        return $this->getTableLocator()->get('ContentBlocks.ContentBlocks')
            ->find()->where(['slug' => $slug])->firstOrFail()->value;
    }

    /**
     * Test that only admins can open site content
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->get('/content-blocks');
        $this->assertRedirectsToLogin();

        $this->loginAs('testclinician');
        $this->get('/content-blocks');
        $this->assertRedirectContains('/dashboard/staff');

        $this->loginAs('testpatient');
        $this->get('/content-blocks');
        $this->assertRedirectContains('/dashboard/patient');

        $this->loginAs('testadmin');
        $this->get('/content-blocks');
        $this->assertResponseOk();
        $this->assertResponseContains('Site Name');
    }

    /**
     * Test editing a text block
     *
     * @return void
     */
    public function testEdit(): void
    {
        $block = $this->block('global-site-name');
        $url = '/content-blocks/edit/' . $block->id;

        $this->loginAs('testclinician');
        $this->post($url, ['value' => 'Hacked']);
        $this->assertRedirectContains('/dashboard/staff');
        $this->assertSame('RespiraCare', $this->value('global-site-name'));

        $this->loginAs('testadmin');
        $this->get($url);
        $this->assertResponseOk();

        // HTML is stripped, and the old value is kept for restore
        $this->post($url, ['value' => '<b>New Name</b>']);
        $this->assertRedirectContains('/content-blocks');
        $this->assertSame('New Name', $this->value('global-site-name'));
        $this->assertSame('RespiraCare', $this->block('global-site-name')->previous_value);
    }

    /**
     * Test length and icon rules
     *
     * @return void
     */
    public function testEditRejectsInvalidValues(): void
    {
        $this->loginAs('testadmin');

        // The site name is limited to 40 characters
        $block = $this->block('global-site-name');
        $this->post('/content-blocks/edit/' . $block->id, ['value' => str_repeat('x', 41)]);
        $this->assertResponseOk();
        $this->assertFlashMessage('This content block must be 40 characters or fewer.');
        $this->assertSame('RespiraCare', $this->value('global-site-name'));

        $icon = $this->getTableLocator()->get('ContentBlocks.ContentBlocks')
            ->find()->where(['slug LIKE' => '%-icon', 'type' => 'text'])->firstOrFail();
        $before = $this->block($icon->slug)->value;
        $this->post('/content-blocks/edit/' . $icon->id, ['value' => 'not an icon']);
        $this->assertResponseOk();
        $this->assertSame($before, $this->value($icon->slug));
    }

    /**
     * Test restoring the previous value
     *
     * @return void
     */
    public function testRestore(): void
    {
        $block = $this->block('global-site-name');
        $url = '/content-blocks/restore/' . $block->id;

        // Nothing to restore yet
        $this->loginAs('testadmin');
        $this->post($url);
        $this->assertFlashMessage('There is no previous value to restore.');

        $this->post('/content-blocks/edit/' . $block->id, ['value' => 'New Name']);

        $this->loginAs('testclinician');
        $this->post($url);
        $this->assertSame('New Name', $this->value('global-site-name'));

        $this->loginAs('testadmin');
        $this->post($url);
        $this->assertRedirectContains('/content-blocks');
        $this->assertSame('RespiraCare', $this->value('global-site-name'));
    }

    /**
     * Test image upload rules
     *
     * @return void
     */
    public function testUpload(): void
    {
        $this->loginAs('testclinician');
        $this->post('/content-blocks/upload', []);
        $this->assertResponseCode(403);

        $this->loginAs('testadmin');
        $this->post('/content-blocks/upload', []);
        $this->assertResponseCode(400);

        // A PHP file renamed and labelled as an image is rejected
        $path = tempnam(sys_get_temp_dir(), 'upload');
        file_put_contents($path, '<?php echo 1;');
        $this->post('/content-blocks/upload', [
            'upload' => new UploadedFile($path, 13, UPLOAD_ERR_OK, 'shell.png', 'image/png'),
        ]);
        $this->assertResponseCode(400);
        $this->post('/content-blocks/upload', [
            'upload' => new UploadedFile($path, 13, UPLOAD_ERR_OK, 'shell.php', 'image/png'),
        ]);
        $this->assertResponseCode(400);
        unlink($path);

        // A real image is stored
        $path = tempnam(sys_get_temp_dir(), 'upload');
        file_put_contents($path, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
        ));
        // Real image bytes don't make a .php name acceptable
        $copy = $path . '.copy';
        copy($path, $copy);
        $this->post('/content-blocks/upload', [
            'upload' => new UploadedFile($copy, (int)filesize($copy), UPLOAD_ERR_OK, 'shell.php', 'image/png'),
        ]);
        $this->assertResponseCode(400);
        if (file_exists($copy)) {
            unlink($copy);
        }

        $this->post('/content-blocks/upload', [
            'upload' => new UploadedFile($path, (int)filesize($path), UPLOAD_ERR_OK, 'logo.png', 'image/png'),
        ]);
        $this->assertResponseOk();
        $url = json_decode((string)$this->_response->getBody(), true)['url'];
        $this->assertStringContainsString('/img/content-blocks/', $url);
        $stored = WWW_ROOT . 'img' . DS . 'content-blocks' . DS . basename($url);
        $this->assertFileExists($stored);
        unlink($stored);
    }
}
