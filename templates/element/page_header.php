<?php
/**
 * Shared dashboard page header with universal back button.
 *
 * @var \App\View\AppView $this
 * @var string|null $title Page title
 * @var string|null $subtitle Optional subtitle/description
 * @var string|null $backUrl URL to go back to
 * @var bool $showBack Whether to show back button
 */
$showBack = $showBack ?? true;
$backUrl = $backUrl ?? null;
$title = $title ?? null;
$subtitle = $subtitle ?? null;

$renderBackButton = function () use ($showBack, $backUrl) {
    if (!$showBack) {
        return '';
    }

    if ($backUrl) {
        return $this->Html->link(
            '<i class="bi bi-arrow-left"></i>',
            $backUrl,
            [
                'class' => 'rc-back-button back-button',
                'escape' => false,
                'title' => __('Go back'),
                'aria-label' => __('Go back'),
            ]
        );
    }

    return '<button type="button" onclick="window.history.back()" class="rc-back-button back-button" title="' . h(__('Go back')) . '" aria-label="' . h(__('Go back')) . '"><i class="bi bi-arrow-left"></i></button>';
};

$hasHeading = $title || $subtitle;
?>

<div class="rc-dashboard-page-header rc-page-header">
    <?php if ($hasHeading): ?>
        <div class="rc-dashboard-header-main rc-page-header-main">
            <div class="rc-dashboard-header-copy rc-page-header-copy">
                <div class="rc-dashboard-title-row rc-page-title-row">
                    <?= $renderBackButton() ?>

                    <?php if ($title): ?>
                        <h1 class="rc-dashboard-title rc-page-title page-title"><?= h($title) ?></h1>
                    <?php endif; ?>
                </div>

                <?php if ($subtitle): ?>
                    <p class="rc-dashboard-subtitle rc-page-subtitle page-subtitle"><?= h($subtitle) ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif ($showBack): ?>
        <div class="rc-page-back-row">
            <?= $renderBackButton() ?>
        </div>
    <?php endif; ?>
</div>
