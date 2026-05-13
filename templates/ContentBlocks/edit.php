<?php
/**
 * @var \App\View\AppView $this
 * @var \ContentBlocks\Model\Entity\ContentBlock $contentBlock
 */

$this->assign('title', 'Edit Content Block - Content Blocks');

$this->Html->script('ContentBlocks.ckeditor/ckeditor', ['block' => true]);
$this->Html->css('ContentBlocks.content-blocks', ['block' => true]);
?>

<style>
    .ck-editor__editable_inline {
        min-height: 25rem;
    }
</style>

<div class="row">
    <div class="column-responsive">
        <div class="contentBlocks form content">
            <h3 class="content-blocks--form-heading"><?= h($contentBlock->label) ?></h3>

            <div class="content-blocks--form-description">
                <?= h($contentBlock->description) ?>
            </div>

            <?= $this->Form->create($contentBlock, ['type' => 'file']) ?>

            <?php if ($contentBlock->type === 'text'): ?>
                <?= $this->Form->control('value', [
                    'type' => 'text',
                    'value' => html_entity_decode((string)$contentBlock->value),
                    'label' => false,
                ]) ?>
            <?php elseif ($contentBlock->type === 'html'): ?>
                <?= $this->Form->control('value', [
                    'type' => 'textarea',
                    'label' => false,
                    'id' => 'content-value-input',
                ]) ?>

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        CKSource.Editor.create(
                            document.getElementById('content-value-input'),
                            {
                                toolbar: [
                                    'heading', '|',
                                    'bold', 'italic', 'underline', '|',
                                    'bulletedList', 'numberedList', '|',
                                    'alignment', 'blockQuote', '|',
                                    'indent', 'outdent', '|',
                                    'link', '|',
                                    'insertTable', 'imageInsert', 'mediaEmbed', 'horizontalLine', '|',
                                    'removeFormat', '|',
                                    'sourceEditing', '|',
                                    'undo', 'redo',
                                ],
                                simpleUpload: {
                                    uploadUrl: <?= json_encode($this->Url->build([
                                        'plugin' => false,
                                        'controller' => 'ContentBlocks',
                                        'action' => 'upload',
                                    ])) ?>,
                                    headers: {
                                        'X-CSRF-TOKEN': <?= json_encode($this->request->getAttribute('csrfToken')) ?>,
                                    }
                                }
                            }
                        );
                    });
                </script>
            <?php elseif ($contentBlock->type === 'image'): ?>
                <?php if ($contentBlock->value): ?>
                    <?= $this->Html->image($contentBlock->value, [
                        'class' => 'content-blocks--image-preview',
                        'alt' => $contentBlock->label,
                    ]) ?>
                <?php endif; ?>

                <?= $this->Form->control('value', [
                    'type' => 'file',
                    'accept' => 'image/*',
                    'label' => false,
                ]) ?>
            <?php else: ?>
                <p><?= h(__('This content block type is not supported.')) ?></p>
            <?php endif; ?>

            <div class="content-blocks--form-actions">
                <?= $this->Form->button(__('Save'), ['class' => 'button btn']) ?>
                <?= $this->Html->link(
                    __('Cancel'),
                    ['plugin' => false, 'controller' => 'ContentBlocks', 'action' => 'index']
                ) ?>
            </div>

            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
