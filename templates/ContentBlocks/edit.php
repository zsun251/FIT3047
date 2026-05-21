<?php
declare(strict_types=1);

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ContentBlock $contentBlock
 */

$this->assign('title', __('Edit Content'));

$this->Html->css('content-blocks', ['block' => true]);

$this->Html->script([
    'content-block-icons',
    'content-blocks-edit',
], ['block' => true]);

$typeLabels = [
    'text' => __('Text'),
    'image' => __('Image'),
    'icon' => __('Icon'),
];

$type = strtolower((string)($contentBlock->type ?? 'text'));
$slug = strtolower((string)($contentBlock->slug ?? ''));

$isIconBlock = $type === 'icon' || str_ends_with($slug, '-icon');
$blockType = $isIconBlock ? 'icon' : $type;
$typeLabel = $typeLabels[$blockType] ?? ucfirst($blockType);

$blockValue = html_entity_decode((string)($contentBlock->value ?? ''));

$textValueMaxLength = $blockType === 'icon' ? 80 : 1000;
$isLongTextBlock = strlen($blockValue) > 100;

$safeIconClass = preg_match('/^bi-[a-z0-9-]+$/', $blockValue)
    ? $blockValue
    : 'bi-heart-pulse';

$maxImageSize = 2 * 1024 * 1024;
$allowedImageTypes = [
    'image/jpeg',
    'image/png',
    'image/webp',
    'image/gif',
];

$allowedImageExtensions = [
    'jpg',
    'jpeg',
    'png',
    'webp',
    'gif',
];

$backUrl = ['action' => 'index'];
?>

<div class="rc-dashboard-scale-stage rc-dashboard-scale-md rc-dashboard-scale-up">
    <div class="rc-dashboard-scale-content content-blocks-page">
        <div class="rc-dashboard-page-header content-blocks-page-header">
            <div class="rc-dashboard-header-main content-blocks-header-main">
                <div class="rc-dashboard-header-copy content-blocks-header-copy">
                    <div class="rc-dashboard-title-row content-blocks-page-title-row">
                        <?= $this->Html->link(
                            '<i class="bi bi-arrow-left"></i>',
                            $backUrl,
                            [
                                'class' => 'rc-back-button',
                                'escape' => false,
                                'aria-label' => __('Return to previous page'),
                            ],
                        ) ?>

                        <h1 class="rc-dashboard-title"><?= h($contentBlock->label) ?></h1>
                    </div>

                    <p class="rc-dashboard-subtitle">
                        <?= h($contentBlock->description ?: __('Update this content block and save it to apply the change across the website.')) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="content-block-edit-card">
            <div class="content-block-edit-card-header">
                <div>
                    <h2><?= __('Edit Content') ?></h2>
                    <p><?= __('Update this content block and save it to apply the change across the website.') ?></p>
                </div>

                <span class="rc-type-badge content-block-type-badge content-block-type-<?= h($blockType) ?>">
            <?= h($typeLabel) ?>
        </span>
            </div>

            <div class="content-block-edit-card-body">
                <?= $this->Form->create($contentBlock, [
                    'type' => 'file',
                    'class' => 'content-block-edit-form',
                ]) ?>

                <?php if ($blockType === 'icon'): ?>
                    <div class="content-block-icon-editor">
                        <div class="content-block-icon-preview-panel">
                            <div class="content-block-preview-label">
                                <?= __('Selected Icon') ?>
                            </div>

                            <div class="content-block-icon-preview">
                                <i id="content-block-selected-icon" class="bi <?= h($safeIconClass) ?>"></i>
                            </div>

                            <p class="content-block-icon-preview-help">
                                <?= __('Choose an icon below, or type a Bootstrap Icons class manually.') ?>
                            </p>
                        </div>

                        <div class="content-block-icon-control-panel">
                            <?= $this->Form->control('value', [
                                'type' => 'text',
                                'value' => $safeIconClass,
                                'label' => __('Bootstrap Icon Class'),
                                'id' => 'content-block-value',
                                'class' => 'rc-form-control-square content-block-edit-input content-block-square-input',
                                'placeholder' => 'bi-heart-pulse',
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
                                class="rc-form-control-square content-block-icon-search content-block-square-input"
                                placeholder="<?= h(__('Search icons')) ?>"
                            >

                            <div id="content-block-icon-grid" class="content-block-icon-grid"></div>

                            <div id="content-block-icon-empty" class="content-block-icon-empty d-none">
                                <?= __('No matching icons found.') ?>
                            </div>
                        </div>
                    </div>
                <?php elseif ($blockType === 'image'): ?>
                    <div class="content-block-image-editor">
                        <div class="content-block-image-preview-grid">
                            <div class="content-block-image-preview-card">
                                <div class="content-block-preview-label">
                                    <?= __('Current Image') ?>
                                </div>

                                <?php if (!empty($contentBlock->value)): ?>
                                    <?= $this->Html->image($contentBlock->value, [
                                        'alt' => $contentBlock->label,
                                        'class' => 'content-blocks-image-preview',
                                    ]) ?>
                                <?php else: ?>
                                    <div class="content-block-empty-preview">
                                        <i class="bi bi-image"></i>
                                        <span><?= __('No image has been set yet.') ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div id="content-block-new-image-preview-card"
                                 class="content-block-image-preview-card d-none">
                                <div class="content-block-preview-label">
                                    <?= __('New Image Preview') ?>
                                </div>

                                <img
                                    id="content-block-new-image-preview"
                                    class="content-blocks-image-preview"
                                    src=""
                                    alt="<?= h(__('New image preview')) ?>"
                                >
                            </div>
                        </div>

                        <div class="content-block-upload-panel">
                            <h3><?= __('Upload New Image') ?></h3>
                            <p><?= __('Choose a replacement image. The current image will remain active until you save changes.') ?></p>

                            <label class="rc-action-pill rc-action-pill-clear content-block-file-label">
                                <i class="bi bi-cloud-arrow-up"></i>
                                <span><?= __('Choose image') ?></span>

                                <?= $this->Form->file('image_file', [
                                    'id' => 'content-block-image',
                                    'class' => 'content-block-native-file-input',
                                    'accept' => implode(',', $allowedImageTypes),
                                    'data-max-size' => $maxImageSize,
                                    'data-allowed-types' => implode(',', $allowedImageTypes),
                                    'data-allowed-extensions' => implode(',', $allowedImageExtensions),
                                ]) ?>
                            </label>

                            <div id="content-block-file-name" class="content-block-file-name">
                                <?= __('No file selected.') ?>
                            </div>

                            <div id="content-block-image-help" class="content-block-feedback-text">
                                <?= __('Accepted formats: JPG, PNG, WEBP, or GIF. Maximum size: 2MB.') ?>
                            </div>

                            <div id="content-block-image-error" class="content-block-feedback-error d-none">
                                <?= __('Invalid image.') ?>
                            </div>

                            <?= $this->Form->control('value', [
                                'type' => 'hidden',
                                'value' => $blockValue,
                            ]) ?>
                        </div>
                    </div>
                <?php else: ?>
                    <?= $this->Form->control('value', [
                        'type' => 'textarea',
                        'value' => $blockValue,
                        'label' => __('Content Text'),
                        'id' => 'content-block-value',
                        'class' => 'rc-form-control-square content-block-edit-input content-block-square-input content-block-textarea' . ($isLongTextBlock ? ' is-long-text' : ''),
                        'rows' => 7,
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

                <div class="content-block-form-actions">
                    <?= $this->Html->link(
                        '<i class="bi bi-x-circle"></i><span>' . __('Cancel') . '</span>',
                        ['action' => 'index'],
                        [
                            'class' => 'rc-action-pill rc-action-pill-danger content-block-cancel-button',
                            'escape' => false,
                        ],
                    ) ?>

                    <?= $this->Form->button(
                        '<i class="bi bi-check2-circle"></i><span>' . __('Save Changes') . '</span>',
                        [
                            'class' => 'rc-action-pill content-block-save-button',
                            'escapeTitle' => false,
                        ],
                    ) ?>
                </div>

                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
