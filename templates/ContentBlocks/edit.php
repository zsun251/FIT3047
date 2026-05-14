<?php
/**
 * @var \App\View\AppView $this
 * @var \ContentBlocks\Model\Entity\ContentBlock $contentBlock
 * @var int|null $textValueMaxLength
 */

$this->assign('title', 'Edit Content Block - Content Blocks');

$this->Html->css('ContentBlocks.content-blocks', ['block' => true]);
$this->Html->css('content-blocks', ['block' => true]);

$this->Html->script('content-blocks-edit', ['block' => true]);
?>

<div class="row">
    <div class="column-responsive">
        <div class="contentBlocks form content">
            <h3 class="content-blocks--form-heading"><?= h($contentBlock->label) ?></h3>

            <div class="content-blocks--form-description">
                <?= h($contentBlock->description) ?>
            </div>

            <?= $this->Form->create($contentBlock, ['type' => 'file']) ?>

            <?php if ($contentBlock->type === 'text'): ?>
                <?php $textValueMaxLength = $textValueMaxLength ?? 1000; ?>

                <?= $this->Form->control('value', [
                    'type' => 'text',
                    'value' => html_entity_decode((string)$contentBlock->value),
                    'label' => false,
                    'id' => 'content-block-value',
                    'data-max-length' => $textValueMaxLength,
                ]) ?>

                <div id="content-block-character-help" class="content-block-feedback-text">
                    <span id="content-block-character-count">0</span>
                    /
                    <span><?= h($textValueMaxLength) ?></span>
                    <?= __('characters used') ?>
                </div>

                <div id="content-block-character-error" class="content-block-feedback-error d-none">
                    <?= __('Character limit exceeded.') ?>
                </div>
            <?php elseif ($contentBlock->type === 'image'): ?>
                <div class="content-block-image-preview-grid">
                    <div class="content-block-image-preview-card">
                        <div class="content-block-preview-label">
                            <?= __('Current image') ?>
                        </div>

                        <?php if (!empty($contentBlock->value)): ?>
                            <?= $this->Html->image($contentBlock->value, [
                                'class' => 'content-blocks--image-preview content-blocks-image-preview',
                                'alt' => $contentBlock->label,
                            ]) ?>
                        <?php else: ?>
                            <div class="content-block-empty-preview">
                                <?= __('No current image') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div id="content-block-new-image-preview-card" class="content-block-image-preview-card d-none">
                        <div class="content-block-preview-label">
                            <?= __('New image') ?>
                        </div>

                        <img
                            id="content-block-new-image-preview"
                            class="content-blocks--image-preview content-blocks-image-preview"
                            src=""
                            alt="<?= h(__('New image preview')) ?>"
                        >
                    </div>
                </div>

                <?= $this->Form->control('value', [
                'type' => 'file',
                'accept' => 'image/jpeg,image/png,image/gif,image/webp',
                'label' => false,
                'id' => 'content-block-image',
                'data-max-size' => 2097152,
                'data-allowed-types' => 'image/jpeg,image/png,image/gif,image/webp',
                'data-allowed-extensions' => 'jpg,jpeg,png,gif,webp',
            ]) ?>

                <div id="content-block-image-help" class="content-block-feedback-text">
                    <?= __('Allowed image types: JPG, PNG, GIF, or WebP. Maximum size: 2MB.') ?>
                </div>

                <div id="content-block-image-error" class="content-block-feedback-error d-none"></div>
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
