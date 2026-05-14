<?php
/**
 * @var \App\View\AppView $this
 * @var array<string, array<\ContentBlocks\Model\Entity\ContentBlock>> $contentBlocksGrouped
 */

$this->assign('title', 'Site Content');

$this->Html->css('ContentBlocks.content-blocks', ['block' => true]);
$this->Html->css('content-blocks', ['block' => true]);

$this->Html->script('content-blocks-index', ['block' => true]);

$contentBlocksGrouped = $contentBlocksGrouped ?? [];

$homeParents = [
    'home_intro',
    'home_benefits',
    'home_motivational_banner',
    'home_how_it_works',
    'home_getting_started',
    'home_faq',
];

$parentLabels = [
    'global' => 'Global Branding',
    'home_intro' => 'Intro',
    'home_benefits' => 'Benefits',
    'home_motivational_banner' => 'Motivational Banner',
    'home_how_it_works' => 'How It Works',
    'home_getting_started' => 'Getting Started',
    'home_faq' => 'FAQ',
    'contact' => 'Contact Page',
    'resources' => 'Resources',
    'auth' => 'Authentication Pages',
    'dashboard' => 'Dashboards',
    'health_records' => 'Health Records',
    'appointments' => 'Appointments',
    'reminders' => 'Reminders',
];

$parentIcons = [
    'global' => 'bi-globe2',
    'home' => 'bi-house-door',
    'contact' => 'bi-envelope',
    'resources' => 'bi-journal-text',
    'auth' => 'bi-shield-lock',
    'dashboard' => 'bi-speedometer2',
    'health_records' => 'bi-heart-pulse',
    'appointments' => 'bi-calendar-event',
    'reminders' => 'bi-bell',
];

$formatParentLabel = function (string $parent) use ($parentLabels): string {
    if (isset($parentLabels[$parent])) {
        return $parentLabels[$parent];
    }

    return ucwords(str_replace('_', ' ', $parent));
};

$getBlockTypeKey = function ($contentBlock): string {
    if ((string)$contentBlock->type === 'image') {
        return 'image';
    }

    if (str_ends_with((string)$contentBlock->slug, '-icon')) {
        return 'icon';
    }

    return 'text';
};

$getBlockTypeLabel = function ($contentBlock) use ($getBlockTypeKey): string {
    $type = $getBlockTypeKey($contentBlock);

    if ($type === 'image') {
        return 'Image';
    }

    if ($type === 'icon') {
        return 'Icon';
    }

    return 'Text';
};

$getBlockTypeClass = function ($contentBlock) use ($getBlockTypeKey): string {
    $type = $getBlockTypeKey($contentBlock);

    if ($type === 'image') {
        return 'content-block-type-image';
    }

    if ($type === 'icon') {
        return 'content-block-type-icon';
    }

    return 'content-block-type-text';
};

$renderBlockList = function (array $contentBlocks) use ($getBlockTypeKey, $getBlockTypeLabel, $getBlockTypeClass) {
    ?>
    <div class="content-block-list">
        <?php foreach ($contentBlocks as $contentBlock): ?>
            <?php
            $blockType = $getBlockTypeKey($contentBlock);
            $blockValue = (string)$contentBlock->value;
            ?>

            <article class="content-block-item" data-content-block-item data-content-block-type="<?= h($blockType) ?>">
                <div class="content-block-item-preview">
                    <?php if ($blockType === 'image' && !empty($blockValue)): ?>
                        <?= $this->Html->image($blockValue, [
                            'class' => 'content-block-card-image-preview',
                            'alt' => $contentBlock->label,
                        ]) ?>
                    <?php elseif ($blockType === 'icon' && !empty($blockValue)): ?>
                        <span class="content-block-card-icon-preview">
                            <i class="bi <?= h($blockValue) ?>"></i>
                        </span>
                    <?php else: ?>
                        <span class="content-block-card-text-preview">
                            <i class="bi bi-card-text"></i>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="content-block-item-main">
                    <div class="content-block-item-title-row">
                        <h3 class="content-block-item-title">
                            <?= h($contentBlock->label) ?>
                        </h3>

                        <span class="content-block-type-badge <?= h($getBlockTypeClass($contentBlock)) ?>">
                            <?= h($getBlockTypeLabel($contentBlock)) ?>
                        </span>
                    </div>

                    <p class="content-block-item-description">
                        <?= h($contentBlock->description) ?>
                    </p>
                </div>

                <div class="content-block-item-actions">
                    <?= $this->Html->link(
                        '<i class="bi bi-pencil-square"></i><span>Edit</span>',
                        [
                            'plugin' => false,
                            'controller' => 'ContentBlocks',
                            'action' => 'edit',
                            $contentBlock->id,
                        ],
                        [
                            'class' => 'content-block-edit-button',
                            'escape' => false,
                        ]
                    ) ?>

                    <?php if (!empty($contentBlock->previous_value)): ?>
                        <?= $this->Form->postLink(
                            '<i class="bi bi-arrow-counterclockwise"></i><span>Restore</span>',
                            [
                                'plugin' => false,
                                'controller' => 'ContentBlocks',
                                'action' => 'restore',
                                $contentBlock->id,
                            ],
                            [
                                'class' => 'btn btn-outline-secondary btn-sm',
                                'escapeTitle' => false,
                                'confirm' => __(
                                    "Are you sure you want to restore the previous version for this item?\n{0}/{1}\nNote: You cannot cancel this action!",
                                    $contentBlock->parent,
                                    $contentBlock->slug
                                ),
                            ]
                        ) ?>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?php
};

$homeContentBlocksGrouped = [];
$regularContentBlocksGrouped = [];

foreach ($contentBlocksGrouped as $parent => $contentBlocks) {
    if (in_array($parent, $homeParents, true)) {
        $homeContentBlocksGrouped[$parent] = $contentBlocks;
    } else {
        $regularContentBlocksGrouped[$parent] = $contentBlocks;
    }
}

$orderedRegularParents = [
    'global',
    'contact',
    'resources',
    'auth',
    'dashboard',
    'health_records',
    'appointments',
    'reminders',
];

$regularParents = [];

foreach ($orderedRegularParents as $parent) {
    if (isset($regularContentBlocksGrouped[$parent])) {
        $regularParents[] = $parent;
    }
}

foreach (array_keys($regularContentBlocksGrouped) as $parent) {
    if (!in_array($parent, $regularParents, true)) {
        $regularParents[] = $parent;
    }
}

$homeBlockCount = 0;

foreach ($homeContentBlocksGrouped as $contentBlocks) {
    $homeBlockCount += count($contentBlocks);
}
?>

<div class="content-blocks-page">
    <div class="content-blocks-page-header">
        <div>
            <h1><?= __('Site Content') ?></h1>
            <p><?= __('Edit the text, images, and icons used across the public website and patient-facing pages.') ?></p>
        </div>
    </div>

    <div class="content-blocks-admin-card">
        <div class="content-blocks-card-header">
            <div>
                <h2><?= __('Editable Content Sections') ?></h2>
                <p><?= __('Open a section below, choose the content item you want to change, then use Edit to update the saved value.') ?></p>
            </div>
        </div>

        <div class="content-block-filter-panel">
            <div class="content-block-filter-group">
                <div class="content-block-filter-label">
                    <?= __('Content Type') ?>
                </div>

                <div class="content-block-filter-controls" aria-label="<?= h(__('Filter content blocks by type')) ?>">
                    <button class="content-block-filter-button active" type="button" data-content-block-filter="all" aria-pressed="true">
                        <?= __('All') ?>
                    </button>
                    <button class="content-block-filter-button" type="button" data-content-block-filter="text" aria-pressed="false">
                        <?= __('Text') ?>
                    </button>
                    <button class="content-block-filter-button" type="button" data-content-block-filter="image" aria-pressed="false">
                        <?= __('Images') ?>
                    </button>
                    <button class="content-block-filter-button" type="button" data-content-block-filter="icon" aria-pressed="false">
                        <?= __('Icons') ?>
                    </button>
                </div>
            </div>
        </div>

        <?php if (!empty($contentBlocksGrouped)): ?>
            <div class="content-block-section-list" id="contentBlocksAccordion">
                <?php if (!empty($homeContentBlocksGrouped)): ?>
                    <section class="content-block-section" data-content-block-section>
                        <button
                            class="content-block-section-toggle collapsed"
                            type="button"
                            data-content-block-toggle="content-block-section-home"
                            aria-expanded="false"
                            aria-controls="content-block-section-home"
                        >
                            <span class="content-block-section-icon">
                                <i class="bi bi-house-door"></i>
                            </span>

                            <span class="content-block-section-title">
                                <?= __('Home Page') ?>
                            </span>

                            <span class="content-block-section-count" data-content-block-count>
                                <?= __('{0} items', $homeBlockCount) ?>
                            </span>

                            <i class="bi bi-chevron-down content-block-section-chevron"></i>
                        </button>

                        <div id="content-block-section-home" class="content-block-section-body d-none">
                            <div class="content-block-home-subsection-list">
                                <?php foreach ($homeParents as $homeParent): ?>
                                    <?php if (!empty($homeContentBlocksGrouped[$homeParent])): ?>
                                        <?php $homeSubsectionId = 'content-block-home-subsection-' . strtolower(trim((string)preg_replace('/[^A-Za-z0-9-]+/', '-', $homeParent), '-')); ?>

                                        <section class="content-block-home-subsection" data-content-block-subsection>
                                            <button
                                                class="content-block-home-subsection-toggle collapsed"
                                                type="button"
                                                data-content-block-subtoggle="<?= h($homeSubsectionId) ?>"
                                                aria-expanded="false"
                                                aria-controls="<?= h($homeSubsectionId) ?>"
                                            >
                                                <span class="content-block-home-subsection-title">
                                                    <?= h($formatParentLabel($homeParent)) ?>
                                                </span>

                                                <span class="content-block-home-subsection-count" data-content-block-count>
                                                    <?= __('{0} items', count($homeContentBlocksGrouped[$homeParent])) ?>
                                                </span>

                                                <i class="bi bi-chevron-down content-block-home-subsection-chevron"></i>
                                            </button>

                                            <div id="<?= h($homeSubsectionId) ?>" class="content-block-home-subsection-body d-none">
                                                <?php $renderBlockList($homeContentBlocksGrouped[$homeParent]); ?>
                                            </div>
                                        </section>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <?php foreach ($regularParents as $parent): ?>
                    <?php
                    $contentBlocks = $regularContentBlocksGrouped[$parent];
                    $sectionId = 'content-block-section-' . strtolower(trim((string)preg_replace('/[^A-Za-z0-9-]+/', '-', $parent), '-'));
                    $sectionIcon = $parentIcons[$parent] ?? 'bi-card-text';
                    ?>

                    <section class="content-block-section" data-content-block-section>
                        <button
                            class="content-block-section-toggle collapsed"
                            type="button"
                            data-content-block-toggle="<?= h($sectionId) ?>"
                            aria-expanded="false"
                            aria-controls="<?= h($sectionId) ?>"
                        >
                            <span class="content-block-section-icon">
                                <i class="bi <?= h($sectionIcon) ?>"></i>
                            </span>

                            <span class="content-block-section-title">
                                <?= h($formatParentLabel($parent)) ?>
                            </span>

                            <span class="content-block-section-count" data-content-block-count>
                                <?= __('{0} items', count($contentBlocks)) ?>
                            </span>

                            <i class="bi bi-chevron-down content-block-section-chevron"></i>
                        </button>

                        <div id="<?= h($sectionId) ?>" class="content-block-section-body d-none">
                            <?php $renderBlockList($contentBlocks); ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>

            <div id="content-block-filter-empty" class="content-block-empty-state d-none">
                <i class="bi bi-funnel"></i>
                <h2><?= __('No matching content blocks') ?></h2>
                <p><?= __('Choose another filter to view editable content blocks.') ?></p>
            </div>
        <?php else: ?>
            <div class="content-block-empty-state">
                <i class="bi bi-card-text"></i>
                <h2><?= __('No content blocks found') ?></h2>
                <p><?= __('Run the content block seed to create editable website content.') ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
