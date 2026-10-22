<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Response;
use Cake\Routing\Router;
use ContentBlocks\Model\Entity\ContentBlock;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Content Blocks Controller
 *
 * @property \ContentBlocks\Model\Table\ContentBlocksTable $ContentBlocks
 */
class ContentBlocksController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();

        $this->viewBuilder()->setLayout('app');
        $this->loadComponent('Flash');

        $this->ContentBlocks = $this->fetchTable('ContentBlocks.ContentBlocks');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $contentBlock = $this->ContentBlocks->newEmptyEntity();

        // Check authorization
        if (!$this->Authorization->can($contentBlock, 'index')) {
            $this->Flash->error(__('You do not have permission to access that page.'));

            return $this->redirect($this->getRedirectUrl());
        }

        $contentBlocks = $this->ContentBlocks->find()
            ->orderBy(['ContentBlocks.parent' => 'ASC', 'ContentBlocks.label' => 'ASC',])->all();

        $contentBlocksGrouped = [];

        foreach ($contentBlocks as $block) {
            $parent = $block->parent ?: 'Other';
            $contentBlocksGrouped[$parent][] = $block;
        }

        $this->set(compact('contentBlocksGrouped'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Content Block id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     */
    public function edit($id = null)
    {
        $contentBlock = $this->ContentBlocks->get($id);

        // Check authorization
        if (!$this->Authorization->can($contentBlock, 'edit')) {
            $this->Flash->error(__('You do not have permission to edit this content block.'));

            return $this->redirect($this->getRedirectUrl());
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->prepareContentBlockData($contentBlock, $this->request->getData());

            if ($data !== null) {
                if (array_key_exists('value', $data) && $data['value'] !== $contentBlock->value) {
                    $data['previous_value'] = $contentBlock->value;
                }

                $contentBlock = $this->ContentBlocks->patchEntity($contentBlock, $data);

                if ($this->ContentBlocks->save($contentBlock)) {
                    $this->Flash->success(__('The content block has been saved.'));

                    return $this->redirect(['action' => 'index']);
                }

                $this->Flash->error(__('The content block could not be saved. Please, try again.'));
            }
        }

        $textValueMaxLength = $contentBlock->type === 'text' ? $this->getTextValueLimit($contentBlock) : null;

        $this->set(compact('contentBlock', 'textValueMaxLength'));
    }

    /**
     * Restore method
     *
     * @param string|null $id Content Block id.
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function restore($id = null)
    {
        $this->request->allowMethod(['post']);

        $contentBlock = $this->ContentBlocks->get($id);

        // Check authorization
        if (!$this->Authorization->can($contentBlock, 'restore')) {
            $this->Flash->error(__('You do not have permission to restore this content block.'));

            return $this->redirect($this->getRedirectUrl());
        }

        if (empty($contentBlock->previous_value)) {
            $this->Flash->error(__('There is no previous value to restore.'));
            return $this->redirect(['plugin' => false, 'controller' => 'ContentBlocks', 'action' => 'index']);
        }

        $currentValue = $contentBlock->value;
        $contentBlock->value = $contentBlock->previous_value;
        $contentBlock->previous_value = $currentValue;

        if ($this->ContentBlocks->save($contentBlock)) {
            $this->Flash->success(__('The previous content block value has been restored.'));
        } else {
            $this->Flash->error(__('The content block could not be restored. Please, try again.'));
        }

        return $this->redirect(['plugin' => false, 'controller' => 'ContentBlocks', 'action' => 'index']);
    }

    /**
     * Upload method
     *
     * @return \Cake\Http\Response
     */
    public function upload(): Response
    {
        $contentBlock = $this->ContentBlocks->newEmptyEntity();

        // Check authorization
        if (!$this->Authorization->can($contentBlock, 'upload')) {
            return $this->jsonResponse(
                ['error' => ['message' => __('You do not have permission to upload content block images.'),],],
                403
            );
        }

        $uploadedFile = $this->request->getData('upload');

        if (!$uploadedFile instanceof UploadedFileInterface) {
            return $this->jsonResponse(['error' => ['message' => __('No upload file was provided.'),],], 400);
        }

        $uploadedPath = $this->storeUploadedImage($uploadedFile);

        if ($uploadedPath === null) {
            return $this->jsonResponse([
                'error' => [
                    'message' => __('The uploaded image must be a valid JPG, PNG, GIF, or WebP file under 2MB.'),
                ],
            ], 400);
        }

        return $this->jsonResponse([
            'url' => Router::url('/img/' . $uploadedPath, true),
        ]);
    }

    /**
     * Prepare content block form data before saving.
     *
     * @param \ContentBlocks\Model\Entity\ContentBlock $contentBlock Content block being saved.
     * @param array $data Request data.
     * @return array|null
     */
    private function prepareContentBlockData(ContentBlock $contentBlock, array $data): ?array
    {
        $preparedData = [
            'value' => $data['value'] ?? null,
        ];

        if ($contentBlock->type === 'image') {
            return $this->prepareImageBlockData($preparedData);
        }

        if ($contentBlock->type !== 'text') {
            $this->Flash->error(__('This content block type is not supported.'));

            return null;
        }

        $preparedData['value'] = $this->sanitiseTextValue((string)$preparedData['value']);
        $textLimit = $this->getTextValueLimit($contentBlock);

        if (mb_strlen($preparedData['value']) > $textLimit) {
            $this->Flash->error(__('This content block must be {0} characters or fewer.', $textLimit));

            return null;
        }

        if ($this->isIconBlock($contentBlock) && !$this->isValidBootstrapIcon($preparedData['value'])) {
            $this->Flash->error(__('Icon values must be valid Bootstrap Icons classes, such as bi-heart-pulse.'));

            return null;
        }

        return $preparedData;
    }

    /**
     * Sanitise normal text content.
     *
     * @param string $value Text value.
     * @return string
     */
    private function sanitiseTextValue(string $value): string
    {
        $value = trim($value);
        $value = strip_tags($value);

        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '';
    }

    /**
     * Get the character limit for a text content block.
     *
     * @param \ContentBlocks\Model\Entity\ContentBlock $contentBlock Content block.
     * @return int
     */
    private function getTextValueLimit(ContentBlock $contentBlock): int
    {
        $slug = (string)$contentBlock->slug;

        if ($slug === 'global-site-name') {
            return 40;
        }

        if ($slug === 'global-footer-copyright') {
            return 80;
        }

        if ($this->isIconBlock($contentBlock)) {
            return 80;
        }

        if (
            str_contains($slug, 'button') ||
            str_contains($slug, 'label')
        ) {
            return 60;
        }

        if (
            str_contains($slug, 'heading') ||
            str_contains($slug, 'title')
        ) {
            return 90;
        }

        if (str_contains($slug, 'question')) {
            return 120;
        }

        if (str_contains($slug, 'placeholder')) {
            return 120;
        }

        return 1000;
    }

    /**
     * Check whether a content block stores a Bootstrap icon class.
     *
     * @param \ContentBlocks\Model\Entity\ContentBlock $contentBlock Content block.
     * @return bool
     */
    private function isIconBlock(ContentBlock $contentBlock): bool
    {
        return str_ends_with((string)$contentBlock->slug, '-icon');
    }

    /**
     * Validate Bootstrap Icon class values.
     *
     * @param string $value Icon class.
     * @return bool
     */
    private function isValidBootstrapIcon(string $value): bool
    {
        return (bool)preg_match('/^bi-[a-z0-9-]{1,80}$/', $value);
    }

    /**
     * Prepare image block data before saving.
     *
     * @param array $data Request data.
     * @return array|null
     */
    private function prepareImageBlockData(array $data): ?array
    {
        $uploadedFile = $data['value'] ?? null;

        if (!$uploadedFile instanceof UploadedFileInterface) {
            unset($data['value']);

            return $data;
        }

        if ($uploadedFile->getError() === UPLOAD_ERR_NO_FILE) {
            unset($data['value']);

            return $data;
        }

        $uploadedPath = $this->storeUploadedImage($uploadedFile);

        if ($uploadedPath === null) {
            $this->Flash->error(__('The uploaded image must be a valid JPG, PNG, GIF, or WebP file under 2MB.'));

            return null;
        }

        $data['value'] = $uploadedPath;

        return $data;
    }

    /**
     * Store an uploaded image in webroot/img/content-blocks.
     *
     * @param \Psr\Http\Message\UploadedFileInterface $uploadedFile Uploaded image.
     * @return string|null Relative image path, or null on failure.
     */
    private function storeUploadedImage(UploadedFileInterface $uploadedFile): ?string
    {
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            return null;
        }

        $maxFileSize = 2 * 1024 * 1024;

        if ($uploadedFile->getSize() === null || $uploadedFile->getSize() > $maxFileSize) {
            return null;
        }

        $originalName = $uploadedFile->getClientFilename() ?: '';
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp',];

        if (!in_array($extension, $allowedExtensions, true)) {
            return null;
        }

        if (!in_array((string)$uploadedFile->getClientMediaType(), $allowedMimeTypes, true)) {
            return null;
        }

        $temporaryPath = $uploadedFile->getStream()->getMetadata('uri');

        if (!is_string($temporaryPath) || !@getimagesize($temporaryPath)) {
            return null;
        }

        $uploadDir = WWW_ROOT . 'img' . DS . 'content-blocks' . DS;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $filename = uniqid('content-block-', true) . '.' . $extension;

        $uploadedFile->moveTo($uploadDir . $filename);

        return 'content-blocks/' . $filename;
    }

    /**
     * Return a JSON response.
     *
     * @param array $data Response data.
     * @param int $status HTTP status code.
     * @return \Cake\Http\Response
     */
    private function jsonResponse(array $data, int $status = 200): Response
    {
        return $this->response->withType('application/json')->withStatus($status)->withStringBody((string)json_encode($data));
    }
}
