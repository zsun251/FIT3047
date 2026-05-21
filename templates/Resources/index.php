<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Resource> $resources
 * @var int $totalCount
 * @var int $publishedCount
 * @var int $draftCount
 * @var int $videoCount
 */

use App\Policy\UserAccess;

$this->assign('title', 'Manage Resources');
echo $this->Html->css('resources-staff', ['block' => true]);

$identity      = $this->request->getAttribute('identity');
$isAdmin       = $identity && UserAccess::isAdmin($identity);
$myClinicianId = $isAdmin ? null : UserAccess::getClinicianId($identity);

$backUrl = $this->request->referer(true);

if (!$backUrl || str_contains($backUrl, '/resources')) {
    $backUrl = ['controller' => 'Dashboard', 'action' => 'staff'];
}

$typeIcons = [
    'article' => 'bi-file-text',
    'video'   => 'bi-play-circle-fill',
    'blog'    => 'bi-journal-text',
    'news'    => 'bi-newspaper',
    'link'    => 'bi-link-45deg',
];

$typeLabels = [
    'article' => 'Article',
    'video'   => 'Video',
    'blog'    => 'Blog Post',
    'news'    => 'News',
    'link'    => 'External Link',
];
?>

<div class="resources-page">

    <div class="rc-dashboard-page-header resources-page-header">
        <div class="rc-dashboard-header-main resources-header-main">
            <div class="rc-dashboard-header-copy resources-header-copy">
                <div class="rc-dashboard-title-row resources-title-row">
                    <?= $this->Html->link(
                        '<i class="bi bi-arrow-left"></i>',
                        $backUrl,
                        [
                            'class' => 'rc-back-button resources-back-button',
                            'escape' => false,
                            'aria-label' => 'Return to dashboard',
                        ],
                    ) ?>

                    <h1 class="rc-dashboard-title">Resource Library</h1>
                </div>

                <p class="rc-dashboard-subtitle">Manage educational resources for patients</p>
            </div>

            <?= $this->Html->link(
                '<i class="bi bi-plus-circle"></i><span>Add Resource</span>',
                ['action' => 'add'],
                ['class' => 'rc-action-pill resources-main-btn', 'escape' => false],
            ) ?>
        </div>
    </div>

    <!-- Stats -->
    <div class="resources-stats-grid">
        <div class="resources-stat-card">
            <div class="resources-stat-icon blue">
                <i class="bi bi-journals"></i>
            </div>
            <div>
                <span>Total Resources</span>
                <h2><?= h($totalCount) ?></h2>
                <p>All content</p>
            </div>
        </div>
        <div class="resources-stat-card">
            <div class="resources-stat-icon green">
                <i class="bi bi-check-circle"></i>
            </div>
            <div>
                <span>Published</span>
                <h2><?= h($publishedCount) ?></h2>
                <p>Visible to patients</p>
            </div>
        </div>
        <div class="resources-stat-card">
            <div class="resources-stat-icon orange">
                <i class="bi bi-pencil-square"></i>
            </div>
            <div>
                <span>Drafts</span>
                <h2><?= h($draftCount) ?></h2>
                <p>Not yet published</p>
            </div>
        </div>
        <div class="resources-stat-card">
            <div class="resources-stat-icon purple">
                <i class="bi bi-play-circle"></i>
            </div>
            <div>
                <span>Videos</span>
                <h2><?= h($videoCount) ?></h2>
                <p>Video resources</p>
            </div>
        </div>
    </div>

    <!-- Panel -->
    <div class="resources-panel">

        <div class="resources-toolbar">
            <!-- Type filter tabs -->
            <div class="resources-tabs">
                <?php $currentType = $this->request->getQuery('type'); ?>
                <?= $this->Html->link('All',        ['action' => 'index'],                                  ['class' => 'resource-filter' . (!$currentType               ? ' active' : '')]) ?>
                <?= $this->Html->link('Articles',   ['action' => 'index', '?' => ['type' => 'article']],    ['class' => 'resource-filter' . ($currentType === 'article'  ? ' active' : '')]) ?>
                <?= $this->Html->link('Videos',     ['action' => 'index', '?' => ['type' => 'video']],      ['class' => 'resource-filter' . ($currentType === 'video'    ? ' active' : '')]) ?>
                <?= $this->Html->link('Blog Posts', ['action' => 'index', '?' => ['type' => 'blog']],       ['class' => 'resource-filter' . ($currentType === 'blog'     ? ' active' : '')]) ?>
                <?= $this->Html->link('News',       ['action' => 'index', '?' => ['type' => 'news']],       ['class' => 'resource-filter' . ($currentType === 'news'     ? ' active' : '')]) ?>
                <?= $this->Html->link('Links',      ['action' => 'index', '?' => ['type' => 'link']],       ['class' => 'resource-filter' . ($currentType === 'link'     ? ' active' : '')]) ?>
            </div>

            <!-- Search -->
            <div class="resources-search">
                <i class="bi bi-search"></i>
                <input type="text" id="resourceSearchInput" placeholder="Search resources...">
            </div>
        </div>

        <?php if (!empty($resources) && count($resources) > 0): ?>
            <div class="resources-table">
                <div class="resources-table-head">
                    <div>Resource</div>
                    <div>Author</div>
                    <div>Published</div>
                    <div>Status</div>
                    <div>Actions</div>
                </div>

                <?php foreach ($resources as $resource): ?>
                    <?php
                    $canModify  = $isAdmin || $resource->clinician_id === $myClinicianId;
                    $icon       = $typeIcons[$resource->type] ?? 'bi-file-earmark';
                    $typeLabel  = $typeLabels[$resource->type] ?? ucfirst($resource->type);
                    $authorName = isset($resource->clinician->user)
                        ? h($resource->clinician->user->first_name . ' ' . $resource->clinician->user->last_name)
                        : '—';
                    $searchText = strtolower(
                        $resource->title . ' ' . $typeLabel . ' ' . strip_tags($authorName) . ' ' . $resource->type
                    );
                    ?>
                    <div class="resource-row" data-search="<?= h($searchText) ?>">

                        <div class="resource-row-main">
                            <div class="resource-row-icon type-<?= h($resource->type) ?>">
                                <i class="bi <?= h($icon) ?>"></i>
                            </div>
                            <div>
                                <div class="resource-row-title"><?= h($resource->title) ?></div>
                                <div class="resource-row-type"><?= h($typeLabel) ?></div>
                            </div>
                        </div>

                        <div class="resource-row-author"><?= $authorName ?></div>

                        <div class="resource-row-date">
                            <?php if ($resource->published_at): ?>
                                <strong><?= h($resource->published_at->format('M d, Y')) ?></strong>
                            <?php else: ?>
                                <span>—</span>
                            <?php endif; ?>
                        </div>

                        <div>
                            <span class="resource-status-badge status-<?= h($resource->status) ?>">
                                <?= h(ucfirst($resource->status)) ?>
                            </span>
                        </div>

                        <div class="resource-row-actions">
                            <?= $this->Html->link(
                                '<i class="bi bi-eye"></i>',
                                ['action' => 'view', $resource->id],
                                ['class' => 'resource-action-btn view', 'escape' => false, 'title' => 'View']
                            ) ?>
                            <?php if ($canModify): ?>
                                <?= $this->Html->link(
                                    '<i class="bi bi-pencil"></i>',
                                    ['action' => 'edit', $resource->id],
                                    ['class' => 'resource-action-btn edit', 'escape' => false, 'title' => 'Edit']
                                ) ?>
                                <?= $this->Form->postLink(
                                    '<i class="bi bi-trash"></i>',
                                    ['action' => 'delete', $resource->id],
                                    [
                                        'method'  => 'delete',
                                        'confirm' => __('Delete "{0}"? This cannot be undone.', $resource->title),
                                        'class'   => 'resource-action-btn delete',
                                        'escape'  => false,
                                        'title'   => 'Delete',
                                        'block'   => true,
                                    ]
                                ) ?>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
            <?= $this->fetch('postLink') ?>

            <div class="resources-footer">
                Showing <?= h(count($resources)) ?> resource<?= count($resources) === 1 ? '' : 's' ?>
            </div>

        <?php else: ?>
            <div class="resources-empty-state">
                <div class="resources-empty-icon">
                    <i class="bi bi-journal-x"></i>
                </div>
                <h3>No resources yet</h3>
                <p>Create your first resource to share with patients.</p>
                <?= $this->Html->link(
                    '<i class="bi bi-plus-circle"></i><span>Add Resource</span>',
                    ['action' => 'add'],
                    ['class' => 'rc-action-pill resources-main-btn', 'escape' => false],
                ) ?>
            </div>
        <?php endif; ?>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('resourceSearchInput');
        const rows = document.querySelectorAll('.resource-row');
        if (!searchInput) return;
        searchInput.addEventListener('input', function () {
            const value = this.value.toLowerCase();
            rows.forEach(function (row) {
                row.style.display = (row.dataset.search || '').includes(value) ? 'grid' : 'none';
            });
        });
    });
</script>
