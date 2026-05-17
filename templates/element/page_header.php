<?php
/**
 * Page Header Element with Back Button and Breadcrumbs
 *
 * @var \App\View\AppView $this
 * @var string|null $title Page title (optional)
 * @var string|null $subtitle Optional subtitle/description
 * @var array $breadcrumbs Array of breadcrumb items
 * @var string|null $backUrl URL to go back to (if not using browser back)
 * @var bool $showBack Whether to show back button (default: true)
 */
$showBack = $showBack ?? true;
$breadcrumbs = $breadcrumbs ?? [];
$backUrl = $backUrl ?? null;
$title = $title ?? null;
$subtitle = $subtitle ?? null;

// Load the page header CSS
echo $this->Html->css('page-header', ['block' => true]);
?>

<!-- Desktop Version -->
<div class="page-header-wrapper d-none d-md-block">
    <!-- Back Button Row -->
    <?php if ($showBack): ?>
        <div class="mb-3">
            <?php if ($backUrl): ?>
                <?= $this->Html->link(
                    '<i class="bi bi-arrow-left"></i>',
                    $backUrl,
                    [
                        'class' => 'back-button',
                        'escape' => false,
                        'title' => 'Go back'
                    ]
                ) ?>
            <?php else: ?>
                <button onclick="window.history.back()"
                        class="back-button"
                        title="Go back">
                    <i class="bi bi-arrow-left"></i>
                </button>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Breadcrumbs -->
    <?php if (!empty($breadcrumbs)): ?>
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <?php foreach ($breadcrumbs as $index => $crumb): ?>
                    <?php if ($index === count($breadcrumbs) - 1): ?>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= h($crumb['title']) ?>
                        </li>
                    <?php else: ?>
                        <li class="breadcrumb-item">
                            <?= $this->Html->link(
                                h($crumb['title']),
                                $crumb['url'] ?? '#'
                            ) ?>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>
    <?php endif; ?>

    <!-- Title and Subtitle -->
    <?php if ($title || $subtitle): ?>
        <div>
            <?php if ($title): ?>
                <h1 class="page-title mb-2"><?= h($title) ?></h1>
            <?php endif; ?>

            <?php if ($subtitle): ?>
                <p class="page-subtitle mb-0"><?= h($subtitle) ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Mobile Version -->
<div class="page-header-wrapper d-md-none">
    <!-- Title and Subtitle Only -->
    <?php if ($title || $subtitle): ?>
        <div>
            <?php if ($title): ?>
                <h1 class="page-title mb-2"><?= h($title) ?></h1>
            <?php endif; ?>

            <?php if ($subtitle): ?>
                <p class="page-subtitle mb-0"><?= h($subtitle) ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
