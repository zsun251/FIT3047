<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Resource> $resources
 */
$this->assign('title', 'Resource Library');

// CSS
echo $this->Html->css('resources-browse', ['block' => true]);
?>

<div class="resources-browse">
    <!-- Hero Header -->
    <div class="resource-hero">
        <div class="hero-content">
            <h1><i class="bi bi-book"></i> Resource Library</h1>
            <p class="hero-subtitle">Discover educational materials and expert insights about respiratory health</p>
        </div>
    </div>

    <!-- Filter Pills -->
    <div class="resource-filters-wrapper">
        <div class="resource-filters">
            <?php
            $currentType = $this->request->getQuery('type');
            ?>
            <?= $this->Html->link(
                '<i class="bi bi-grid-3x3"></i> All Resources',
                ['action' => 'browse'],
                [
                    'class' => 'filter-pill' . (!$currentType ? ' active' : ''),
                    'escape' => false
                ]
            ) ?>
            <?= $this->Html->link(
                '<i class="bi bi-file-text"></i> Articles',
                ['action' => 'browse', '?' => ['type' => 'article']],
                [
                    'class' => 'filter-pill' . ($currentType === 'article' ? ' active' : ''),
                    'escape' => false
                ]
            ) ?>
            <?= $this->Html->link(
                '<i class="bi bi-play-circle"></i> Videos',
                ['action' => 'browse', '?' => ['type' => 'video']],
                [
                    'class' => 'filter-pill' . ($currentType === 'video' ? ' active' : ''),
                    'escape' => false
                ]
            ) ?>
            <?= $this->Html->link(
                '<i class="bi bi-journal-text"></i> Blog Posts',
                ['action' => 'browse', '?' => ['type' => 'blog']],
                [
                    'class' => 'filter-pill' . ($currentType === 'blog' ? ' active' : ''),
                    'escape' => false
                ]
            ) ?>
            <?= $this->Html->link(
                '<i class="bi bi-newspaper"></i> News',
                ['action' => 'browse', '?' => ['type' => 'news']],
                [
                    'class' => 'filter-pill' . ($currentType === 'news' ? ' active' : ''),
                    'escape' => false
                ]
            ) ?>
            <?= $this->Html->link(
                '<i class="bi bi-link-45deg"></i> Links',
                ['action' => 'browse', '?' => ['type' => 'link']],
                [
                    'class' => 'filter-pill' . ($currentType === 'link' ? ' active' : ''),
                    'escape' => false
                ]
            ) ?>
        </div>
    </div>

    <!-- Resources Grid -->
    <?php if (!empty($resources) && count($resources) > 0): ?>
        <div class="resources-grid">
            <?php foreach ($resources as $resource): ?>
                <div class="resource-card">
                    <?php if ($resource->thumbnail_url): ?>
                        <div class="resource-thumbnail">
                            <img src="<?= h($resource->thumbnail_url) ?>" alt="<?= h($resource->title) ?>">
                            <div class="thumbnail-overlay">
                                <span class="resource-type-badge type-<?= h($resource->type) ?>">
                                    <?php
                                    $icons = [
                                        'article' => 'file-text',
                                        'video' => 'play-circle-fill',
                                        'blog' => 'journal-text',
                                        'news' => 'newspaper',
                                        'link' => 'link-45deg'
                                    ];
                                    $icon = $icons[$resource->type] ?? 'file-earmark';
                                    ?>
                                    <i class="bi bi-<?= $icon ?>"></i>
                                    <?= h(ucfirst($resource->type)) ?>
                                </span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="resource-thumbnail resource-thumbnail-placeholder">
                            <div class="placeholder-icon">
                                <?php
                                $icons = [
                                    'article' => 'file-text',
                                    'video' => 'play-circle',
                                    'blog' => 'journal-text',
                                    'news' => 'newspaper',
                                    'link' => 'link-45deg'
                                ];
                                $icon = $icons[$resource->type] ?? 'file-earmark';
                                ?>
                                <i class="bi bi-<?= $icon ?>"></i>
                            </div>
                            <span class="resource-type-badge type-<?= h($resource->type) ?>">
                                <i class="bi bi-<?= $icon ?>"></i>
                                <?= h(ucfirst($resource->type)) ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="resource-content">
                        <h3 class="resource-title">
                            <?= h($resource->title) ?>
                        </h3>

                        <?php if ($resource->body): ?>
                            <p class="resource-excerpt">
                                <?= $this->Text->truncate(strip_tags($resource->body), 120, ['ellipsis' => '...', 'exact' => false]) ?>
                            </p>
                        <?php endif; ?>

                        <div class="resource-meta">
                            <?php if ($resource->published_at): ?>
                                <span class="meta-item">
                                    <i class="bi bi-calendar3"></i>
                                    <?= $resource->published_at->format('M d, Y') ?>
                                </span>
                            <?php endif; ?>

                            <?php if (isset($resource->clinician->user)): ?>
                                <span class="meta-item">
                                    <i class="bi bi-person"></i>
                                    <?= h($resource->clinician->user->first_name . ' ' . $resource->clinician->user->last_name) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="resource-actions">
                                <?= $this->Html->link(
                                    'Read More <i class="bi bi-arrow-right"></i>',
                                    ['action' => 'read', $resource->id],
                                    [
                                        'class' => 'btn-read-more',
                                        'escape' => false
                                    ]
                                ) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            <nav aria-label="Resource pagination">
                <ul class="pagination">
                    <?= $this->Paginator->prev('« Previous', ['escape' => false]) ?>
                    <?= $this->Paginator->numbers() ?>
                    <?= $this->Paginator->next('Next »', ['escape' => false]) ?>
                </ul>
            </nav>
        </div>
    <?php else: ?>
        <div class="no-resources">
            <div class="no-resources-icon">
                <i class="bi bi-inbox"></i>
            </div>
            <h3>No resources found</h3>
            <p>Check back soon for educational materials and helpful information.</p>
        </div>
    <?php endif; ?>
</div>
