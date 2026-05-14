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
            ->orderBy([
                'ContentBlocks.parent' => 'ASC',
                'ContentBlocks.label' => 'ASC',
            ])
            ->all();

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
                    return $this->redirect(['plugin' => false, 'controller' => 'ContentBlocks', 'action' => 'index']);
                }
            }

            $this->Flash->error(__('The content block could not be saved. Please, try again.'));
        }

        $this->set(compact('contentBlock'));
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
            return $this->jsonResponse([
                'error' => [
                    'message' => __('You do not have permission to upload content block images.'),
                ],
            ], 403);
        }

        $uploadedFile = $this->request->getData('upload');

        if (!$uploadedFile instanceof UploadedFileInterface) {
            return $this->jsonResponse([
                'error' => [
                    'message' => __('No upload file was provided.'),
                ],
            ], 400);
        }

        $uploadedPath = $this->storeUploadedImage($uploadedFile);

        if ($uploadedPath === null) {
            return $this->jsonResponse([
                'error' => [
                    'message' => __('The uploaded image could not be saved.'),
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
        if ($contentBlock->type !== 'image') {
            return $data;
        }

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

        $originalName = $uploadedFile->getClientFilename() ?: '';
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

        if (!in_array($extension, $allowedExtensions, true)) {
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
        return $this->response
            ->withType('application/json')
            ->withStatus($status)
            ->withStringBody((string)json_encode($data));
    }
}
