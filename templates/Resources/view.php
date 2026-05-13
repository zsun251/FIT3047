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

$statusClass = $resource->status === 'published' ? 'bg-success' : 'bg-secondary';

$breadcrumbs = [
    ['title' => 'Dashboard',  'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
    ['title' => 'Resources',  'url' => ['controller' => 'Resources',  'action' => 'index']],
    ['title' => h($resource->title), 'url' => null],
];
?>

<?= $this->element('page_header', ['breadcrumbs' => $breadcrumbs]) ?>

<!-- Staff Action Bar -->
<div class="resource-view-header">
    <div style="flex:1; min-width:0;">
        <div style="display:flex; align-items:center; gap:.625rem; margin-bottom:.75rem; flex-wrap:wrap;">
            <span class="type-badge <?= h($cfg['color']) ?>" style="position:static;">
                <i class="bi <?= h($cfg['icon']) ?>"></i>
                <?= h($cfg['label']) ?>
            </span>
            <span class="badge <?= $statusClass ?>"><?= h(ucfirst($resource->status)) ?></span>
        </div>
        <h1 style="font-size:1.625rem; font-weight:700; color:#1f2937; margin:0 0 .75rem 0; line-height:1.3;">
            <?= h($resource->title) ?>
        </h1>
        <div class="resource-view-meta">
            <?php if (isset($resource->clinician->user)): ?>
                <span class="meta-item">
                    <i class="bi bi-person-circle"></i>
                    <?= h($resource->clinician->user->first_name . ' ' . $resource->clinician->user->last_name) ?>
                </span>
            <?php endif; ?>
            <?php if ($resource->published_at): ?>
                <span class="meta-item">
                    <i class="bi bi-calendar3"></i>
                    <?= $resource->published_at->format('F j, Y') ?>
                </span>
            <?php endif; ?>
            <?php if ($resource->body): ?>
                <span class="meta-item">
                    <i class="bi bi-clock"></i>
                    <?= max(1, ceil(str_word_count(strip_tags($resource->body)) / 200)) ?> min read
                </span>
            <?php endif; ?>
            <span class="meta-item">
                <i class="bi bi-hash"></i> ID: <?= $resource->id ?>
            </span>
        </div>
    </div>

    <div class="resource-view-actions">
        <?php if ($canModify): ?>
            <?= $this->Html->link(
                '<i class="bi bi-pencil"></i> Edit',
                ['action' => 'edit', $resource->id],
                ['class' => 'btn btn-primary', 'escape' => false]
            ) ?>
        <?php endif; ?>
        <?= $this->Html->link(
            '<i class="bi bi-eye"></i> Patient View',
            ['action' => 'read', $resource->id],
            ['class' => 'btn btn-outline-primary', 'escape' => false]
        ) ?>
        <?php if ($canModify): ?>
            <?= $this->Form->postLink(
                '<i class="bi bi-trash"></i> Delete',
                ['action' => 'delete', $resource->id],
                [
                    'method'  => 'delete',
                    'confirm' => __('Delete "{0}"? This cannot be undone.', $resource->title),
                    'class'   => 'btn btn-outline-danger',
                    'escape'  => false,
                    'block'   => true,
                ]
            ) ?>
        <?php endif; ?>
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

<!-- Thumbnail preview (visible confirmation the value was saved) -->
<?php if ($resource->thumbnail_url): ?>
    <div style="margin-bottom:1.5rem; padding:1rem; background:#f8f9fa; border:1px solid #e9ecef; border-radius:.375rem;">
        <p style="font-size:.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#868e96;margin-bottom:.75rem;">
            <i class="bi bi-image"></i> Thumbnail Image
        </p>
        <div style="display:flex;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
            <img src="<?= h($resource->thumbnail_url) ?>"
                 alt="Thumbnail"
                 style="max-height:120px;max-width:200px;border-radius:.375rem;border:1px solid #dee2e6;object-fit:cover;">
            <div>
                <p style="font-size:.8125rem;color:#6c757d;margin:0;word-break:break-all;"><?= h($resource->thumbnail_url) ?></p>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Featured Image -->
<?php if ($resource->thumbnail_url): ?>
    <div class="resource-view-featured-image" style="max-width:800px; margin:0 auto 2rem;">
        <img src="<?= h($resource->thumbnail_url) ?>" alt="<?= h($resource->title) ?>">
    </div>
<?php endif; ?>

<!-- Content Area -->
<div style="max-width:800px; margin:0 auto;">

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
        <div class="empty-state" style="margin-bottom:2rem;">
            <div class="empty-state-icon"><i class="bi bi-file-earmark-text"></i></div>
            <h3>No content yet</h3>
            <p>Add body text or a URL to give this resource content.</p>
            <?php if ($canModify): ?>
                <?= $this->Html->link(
                    '<i class="bi bi-pencil"></i> Edit Resource',
                    ['action' => 'edit', $resource->id],
                    ['class' => 'btn btn-primary', 'escape' => false]
                ) ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Footer Actions -->
    <div style="display:flex; gap:.75rem; padding-top:1.5rem; border-top:1px solid #e9ecef; flex-wrap:wrap;">
        <?= $this->Html->link(
            '<i class="bi bi-arrow-left"></i> Back to Resources',
            ['action' => 'index'],
            ['class' => 'btn btn-outline-secondary', 'escape' => false]
        ) ?>
    </div>
</div>
