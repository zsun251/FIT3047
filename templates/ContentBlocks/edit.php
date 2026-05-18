<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ContentBlock $contentBlock
 */

$this->assign('title', __('Edit Content'));
$this->Html->css('content-blocks', ['block' => true]);

$typeLabels = [
    'text' => __('Text'),
    'image' => __('Image'),
    'icon' => __('Icon'),
];

$type = (string)$contentBlock->type;
$typeLabel = $typeLabels[$type] ?? ucfirst($type);

$backUrl = ['action' => 'index'];
?>

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
                    ]
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

        <span class="rc-type-badge content-block-type-badge content-block-type-<?= h($type) ?>">
            <?= h($typeLabel) ?>
        </span>
    </div>

    <div class="content-block-edit-card-body">
        <?= $this->Form->create($contentBlock, [
            'type' => 'file',
            'class' => 'content-block-edit-form',
        ]) ?>

        <?php if ($type === 'image'): ?>
            <div class="content-block-image-editor">
                <div class="content-block-current-image">
                    <h3><?= __('Current Image') ?></h3>

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

                <div class="content-block-upload-panel">
                    <h3><?= __('Upload New Image') ?></h3>
                    <p><?= __('Choose a replacement image. The current image will remain active until you save changes.') ?></p>

                    <label class="rc-action-pill rc-action-pill-clear content-block-file-label">
                        <i class="bi bi-cloud-arrow-up"></i>
                        <span><?= __('Choose image') ?></span>
                        <?= $this->Form->file('image_file', [
                            'class' => 'content-block-native-file-input',
                            'accept' => 'image/*',
                        ]) ?>
                    </label>

                    <div class="content-block-file-name" id="contentBlockFileName">
                        <?= __('No file selected') ?>
                    </div>

                    <?= $this->Form->control('value', [
                        'type' => 'hidden',
                    ]) ?>
                </div>
            </div>
        <?php elseif ($type === 'icon'): ?>
            <?php
            $safeIconClass = preg_match('/^bi-[a-z0-9-]+$/', (string)$contentBlock->value)
                ? (string)$contentBlock->value
                : 'bi-heart-pulse';
            ?>

            <div class="content-block-icon-editor">
                <div class="content-block-current-icon">
                    <h3><?= __('Selected Icon') ?></h3>

                    <div class="content-block-icon-preview" id="contentBlockIconPreview">
                        <i class="bi <?= h($safeIconClass) ?>"></i>
                    </div>

                    <p><?= __('Use a Bootstrap Icons class, for example: bi-heart-pulse, bi-shield-check, or bi-chat-dots.') ?></p>
                </div>

                <?= $this->Form->control('value', [
                    'label' => __('Bootstrap Icon Class'),
                    'class' => 'rc-form-control content-block-edit-input',
                    'id' => 'contentBlockIconInput',
                    'placeholder' => 'bi-heart-pulse',
                ]) ?>
            </div>
        <?php else: ?>
            <?= $this->Form->control('value', [
                'label' => __('Content Text'),
                'type' => 'textarea',
                'rows' => 7,
                'class' => 'rc-form-control content-block-edit-input',
            ]) ?>
        <?php endif; ?>

        <div class="content-block-form-actions">
            <?= $this->Html->link(
                '<i class="bi bi-x-circle"></i><span>' . __('Cancel') . '</span>',
                ['action' => 'index'],
                [
                    'class' => 'rc-action-pill rc-action-pill-danger content-block-cancel-button',
                    'escape' => false,
                ]
            ) ?>

            <?= $this->Form->button(
                '<i class="bi bi-check2-circle"></i><span>' . __('Save Changes') . '</span>',
                [
                    'class' => 'rc-action-pill content-block-save-button',
                    'escapeTitle' => false,
                ]
            ) ?>
        </div>

        <?= $this->Form->end() ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.querySelector('.content-block-native-file-input');
        const fileName = document.getElementById('contentBlockFileName');
        const iconInput = document.getElementById('contentBlockIconInput');
        const iconPreview = document.getElementById('contentBlockIconPreview');

        if (fileInput && fileName) {
            fileInput.addEventListener('change', () => {
                fileName.textContent = fileInput.files && fileInput.files.length
                    ? fileInput.files[0].name
                    : '<?= h(__('No file selected')) ?>';
            });
        }

        if (iconInput && iconPreview) {
            iconInput.addEventListener('input', () => {
                const rawIconClass = iconInput.value.trim();
                const iconClass = /^bi-[a-z0-9-]+$/.test(rawIconClass)
                    ? rawIconClass
                    : 'bi-heart-pulse';

                iconPreview.replaceChildren();

                const icon = document.createElement('i');
                icon.classList.add('bi', iconClass);
                iconPreview.appendChild(icon);
            });
        }
    });
</script>
