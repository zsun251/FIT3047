<?php
/**
 * @var \App\View\AppView $this
 * @var \ContentBlocks\Model\Entity\ContentBlock $contentBlock
 * @var int|null $textValueMaxLength
 */

$this->assign('title', 'Edit Site Content');

$this->Html->css('ContentBlocks.content-blocks', ['block' => true]);
$this->Html->css('content-blocks', ['block' => true]);

$this->Html->script('content-blocks-edit', ['block' => true]);

$fallbackBackUrl = $this->Url->build(['controller' => 'Dashboard', 'action' => 'staff']);
$backUrl = $this->request->referer(true) ?: $fallbackBackUrl;

$blockType = (string)$contentBlock->type;
$blockSlug = (string)$contentBlock->slug;
$blockValue = html_entity_decode((string)$contentBlock->value);
$textValueMaxLength = $textValueMaxLength ?? 1000;


$isIconBlock = $blockType === 'text' && str_ends_with($blockSlug, '-icon');
$isLongTextBlock = $blockType === 'text' && !$isIconBlock && mb_strlen($blockValue) > 100;
?>

<div class="content-blocks-page">
    <div class="content-blocks-page-header">
        <div class="content-blocks-page-title-row">
            <?= $this->Html->link(
                '<i class="bi bi-arrow-left"></i>',
                $backUrl,
                [
                    'class' => 'content-blocks-back-button',
                    'escape' => false,
                    'aria-label' => __('Return to previous page'),
                ]
            ) ?>

            <h1><?= h($contentBlock->label) ?></h1>
        </div>

        <p><?= h($contentBlock->description) ?></p>
    </div>

    <div class="content-block-edit-card">
        <div class="content-block-edit-card-header">
            <div>
                <h2><?= __('Edit Content') ?></h2>
                <p><?= __('Update this content block and save it to apply the change across the website.') ?></p>
            </div>

            <?php if ($blockType === 'image'): ?>
                <span class="content-block-type-badge content-block-type-image"><?= __('Image') ?></span>
            <?php elseif ($isIconBlock): ?>
                <span class="content-block-type-badge content-block-type-icon"><?= __('Icon') ?></span>
            <?php else: ?>
                <span class="content-block-type-badge content-block-type-text"><?= __('Text') ?></span>
            <?php endif; ?>
        </div>

        <?= $this->Form->create($contentBlock, ['type' => 'file', 'class' => 'content-block-edit-form']) ?>

        <?php if ($blockType === 'text'): ?>
            <?php if ($isIconBlock): ?>
                <div class="content-block-icon-editor">
                    <div class="content-block-icon-preview-panel">
                        <div class="content-block-preview-label">
                            <?= __('Selected Icon') ?>
                        </div>

                        <div class="content-block-icon-preview">
                            <i id="content-block-selected-icon" class="bi <?= h($blockValue ?: 'bi-question-circle') ?>"></i>
                        </div>

                        <p class="content-block-icon-preview-help">
                            <?= __('Choose an icon below, or type a Bootstrap Icons class manually.') ?>
                        </p>
                    </div>

                    <div class="content-block-icon-control-panel">
                        <?= $this->Form->control('value', [
                            'type' => 'text',
                            'value' => $blockValue,
                            'label' => __('Bootstrap Icon Class'),
                            'id' => 'content-block-value',
                            'class' => 'content-block-edit-input',
                            'data-max-length' => $textValueMaxLength,
                            'data-icon-mode' => '1',
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

                        <label class="content-block-icon-search-label" for="content-block-icon-search">
                            <?= __('Icon Picker') ?>
                        </label>

                        <input
                            type="text"
                            id="content-block-icon-search"
                            class="content-block-icon-search"
                            placeholder="<?= h(__('Search icons')) ?>"
                        >

                        <div id="content-block-icon-grid" class="content-block-icon-grid"></div>

                        <div id="content-block-icon-empty" class="content-block-icon-empty d-none">
                            <?= __('No matching icons found.') ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?= $this->Form->control('value', [
                    'type' => 'textarea',
                    'value' => $blockValue,
                    'label' => __('Content Value'),
                    'id' => 'content-block-value',
                    'class' => 'content-block-edit-input content-block-textarea' . ($isLongTextBlock ? ' is-long-text' : ''),
                    'rows' => 2,
                    'data-max-length' => $textValueMaxLength,
                    'data-auto-grow-text' => '1',
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
            <?php endif; ?>

        <?php elseif ($blockType === 'image'): ?>
            <div class="content-block-image-preview-grid">
                <div class="content-block-image-preview-card">
                    <div class="content-block-preview-label">
                        <?= __('Current Image') ?>
                    </div>

                    <?php if (!empty($contentBlock->value)): ?>
                        <?= $this->Html->image($contentBlock->value, [
                            'class' => 'content-blocks-image-preview',
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
                        <?= __('New Image') ?>
                    </div>

                    <img
                        id="content-block-new-image-preview"
                        class="content-blocks-image-preview"
                        src=""
                        alt="<?= h(__('New image preview')) ?>"
                    >
                </div>
            </div>

            <div class="content-block-file-upload">
                <label class="content-block-file-label" for="content-block-image">
                    <i class="bi bi-upload"></i>
                    <span><?= __('Browse Image') ?></span>
                </label>

                <span id="content-block-file-name" class="content-block-file-name">
        <?= __('No file selected.') ?>
    </span>
            </div>

            <?= $this->Form->control('value', [
            'type' => 'file',
            'accept' => 'image/jpeg,image/png,image/gif,image/webp',
            'label' => false,
            'id' => 'content-block-image',
            'class' => 'content-block-native-file-input',
            'data-max-size' => 2097152,
            'data-allowed-types' => 'image/jpeg,image/png,image/gif,image/webp',
            'data-allowed-extensions' => 'jpg,jpeg,png,gif,webp',
        ]) ?>

            <div id="content-block-image-help" class="content-block-feedback-text">
                <?= __('Allowed image types: JPG, PNG, GIF, or WebP. Maximum size: 2MB.') ?>
            </div>

            <div id="content-block-image-error" class="content-block-feedback-error d-none"></div>

        <?php else: ?>
            <div class="content-block-empty-state">
                <i class="bi bi-exclamation-circle"></i>
                <h2><?= __('Unsupported content type') ?></h2>
                <p><?= __('This content block type cannot be edited from this page.') ?></p>
            </div>
        <?php endif; ?>

        <div class="content-block-edit-actions">
            <?= $this->Html->link(
                __('Cancel'),
                ['plugin' => false, 'controller' => 'ContentBlocks', 'action' => 'index'],
                ['class' => 'content-block-cancel-button']
            ) ?>

            <?= $this->Form->button(__('Save Changes'), ['class' => 'content-block-save-button']) ?>
        </div>

        <?= $this->Form->end() ?>
    </div>
</div>
