<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Resource $resource
 */

use App\Policy\UserAccess;

$this->assign('title', h($resource->title));
echo $this->Html->css('resources-staff', ['block' => true]);
echo $this->Html->css('resource-read',   ['block' => true]);

$identity  = $this->request->getAttribute('identity');
$isAdmin   = $identity && UserAccess::isAdmin($identity);
$canModify = $isAdmin || ($identity && UserAccess::getClinicianId($identity) === $resource->clinician_id);

$typeConfig = [
    'article' => ['icon' => 'bi-file-text',       'label' => 'Article',           'color' => 'type-article'],
    'video'   => ['icon' => 'bi-play-circle-fill', 'label' => 'Video',             'color' => 'type-video'],
    'blog'    => ['icon' => 'bi-journal-text',     'label' => 'Blog Post',         'color' => 'type-blog'],
    'news'    => ['icon' => 'bi-newspaper',        'label' => 'News',              'color' => 'type-news'],
    'link'    => ['icon' => 'bi-link-45deg',       'label' => 'External Resource', 'color' => 'type-link'],
];
$cfg = $typeConfig[$resource->type] ?? $typeConfig['article'];

?>

<div class="rc-dashboard-page-header resources-page-header">
    <div class="rc-dashboard-header-main resources-header-main">
        <div class="rc-dashboard-header-copy">
            <div class="rc-dashboard-title-row">
                <?= $this->Html->link(
                    '<i class="bi bi-arrow-left"></i>',
                    ['action' => 'index'],
                    ['class' => 'rc-back-button', 'escape' => false, 'aria-label' => 'Back to Resources'],
                ) ?>
                <h1 class="rc-dashboard-title"><?= h($resource->title) ?></h1>
            </div>
            <p class="rc-dashboard-subtitle resources-view-subtitle">
                <span class="resource-type-inline">
                    <i class="bi <?= h($cfg['icon']) ?>"></i> <?= h($cfg['label']) ?>
                </span>
                <span class="resource-status-badge status-<?= h($resource->status) ?>">
                    <?= h(ucfirst($resource->status)) ?>
                </span>
                <?php if (isset($resource->clinician->user)): ?>
                    <span class="resources-view-meta-item">
                        <i class="bi bi-person-circle"></i>
                        <?= h($resource->clinician->user->first_name . ' ' . $resource->clinician->user->last_name) ?>
                    </span>
                <?php endif; ?>
                <?php if ($resource->published_at): ?>
                    <span class="resources-view-meta-item">
                        <i class="bi bi-calendar3"></i>
                        <?= $resource->published_at->format('M j, Y') ?>
                    </span>
                <?php endif; ?>
            </p>
        </div>

        <div class="resources-view-actions">
            <?php if ($canModify): ?>
                <?= $this->Html->link(
                    '<i class="bi bi-pencil"></i><span>Edit</span>',
                    ['action' => 'edit', $resource->id],
                    ['class' => 'rc-action-pill', 'escape' => false],
                ) ?>
            <?php endif; ?>
            <?= $this->Html->link(
                '<i class="bi bi-eye"></i><span>Patient View</span>',
                ['action' => 'read', $resource->id],
                ['class' => 'rc-action-pill rc-action-pill-clear', 'escape' => false],
            ) ?>
            <?php if ($canModify): ?>
                <?= $this->Form->postLink(
                    '<i class="bi bi-trash"></i>',
                    ['action' => 'delete', $resource->id],
                    [
                        'method'  => 'delete',
                        'confirm' => __('Delete "{0}"? This cannot be undone.', $resource->title),
                        'class'   => 'rc-icon-action rc-icon-action-danger',
                        'escape'  => false,
                        'title'   => 'Delete',
                        'block'   => true,
                    ],
                ) ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->fetch('postLink') ?>

<!-- Metadata Grid -->
<div class="resource-meta-grid">
    <div class="resource-meta-card">
        <div class="meta-label"><i class="bi bi-tag"></i> Type</div>
        <div class="meta-value"><?= h($cfg['label']) ?></div>
    </div>
    <div class="resource-meta-card">
        <div class="meta-label"><i class="bi bi-broadcast"></i> Status</div>
        <div class="meta-value"><?= h(ucfirst($resource->status)) ?></div>
    </div>
    <div class="resource-meta-card">
        <div class="meta-label"><i class="bi bi-calendar-plus"></i> Created</div>
        <div class="meta-value"><?= $resource->created_at->format('M d, Y H:i') ?></div>
    </div>
    <div class="resource-meta-card">
        <div class="meta-label"><i class="bi bi-pencil-square"></i> Last Updated</div>
        <div class="meta-value"><?= $resource->updated_at->format('M d, Y H:i') ?></div>
    </div>
</div>

<!-- Thumbnail preview -->
<?php if ($resource->thumbnail_url): ?>
    <div class="resources-thumbnail-preview">
        <p class="resources-thumbnail-label"><i class="bi bi-image"></i> Thumbnail Image</p>
        <div class="resources-thumbnail-row">
            <img src="<?= h($resource->thumbnail_url) ?>" alt="Thumbnail" class="resources-thumbnail-img">
            <p class="resources-thumbnail-url"><?= h($resource->thumbnail_url) ?></p>
        </div>
    </div>
<?php endif; ?>

<!-- Featured Image -->
<?php if ($resource->thumbnail_url): ?>
    <div class="resource-view-featured-image resources-featured-image">
        <img src="<?= h($resource->thumbnail_url) ?>" alt="<?= h($resource->title) ?>">
    </div>
<?php endif; ?>

<!-- Content Area -->
<div class="resources-view-content">

    <?php if ($resource->type === 'video' && $resource->url): ?>
        <!-- Video Embed -->
        <div class="resource-content-section">
            <div class="resource-content-label"><i class="bi bi-play-circle"></i> Video</div>
            <?php
            $isYoutube = preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $resource->url, $m)
                      || preg_match('/youtu\.be\/([^\&\?\/]+)/', $resource->url, $m);
            ?>
            <?php if ($isYoutube): ?>
                <div class="video-container">
                    <iframe
                        src="https://www.youtube.com/embed/<?= h($m[1]) ?>"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
            <?php else: ?>
                <a href="<?= h($resource->url) ?>" target="_blank" class="resource-url-link">
                    <i class="bi bi-box-arrow-up-right"></i> <?= h($resource->url) ?>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($resource->body): ?>
        <div class="resource-content-section">
            <div class="resource-content-label"><i class="bi bi-text-paragraph"></i> Content</div>
            <div class="resource-body-preview article-content">
                <?= $this->Text->autoParagraph(h($resource->body)) ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($resource->url && $resource->type !== 'video'): ?>
        <div class="resource-content-section">
            <div class="resource-content-label"><i class="bi bi-link-45deg"></i>
                <?= $resource->type === 'link' ? 'Destination URL' : 'Source URL' ?>
            </div>
            <a href="<?= h($resource->url) ?>" target="_blank" class="resource-url-link">
                <i class="bi bi-box-arrow-up-right"></i>
                <?= h($resource->url) ?>
            </a>
        </div>
    <?php endif; ?>

    <?php if (!$resource->body && !$resource->url): ?>
        <div class="resources-empty-state">
            <div class="resources-empty-icon"><i class="bi bi-file-earmark-text"></i></div>
            <h3>No content yet</h3>
            <p>Add body text or a URL to give this resource content.</p>
            <?php if ($canModify): ?>
                <?= $this->Html->link(
                    '<i class="bi bi-pencil"></i><span>Edit Resource</span>',
                    ['action' => 'edit', $resource->id],
                    ['class' => 'rc-action-pill', 'escape' => false],
                ) ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
