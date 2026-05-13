<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Resource> $resources
 */
$this->assign('title', $this->ContentBlock->text('resources-library-title'));
// CSS
echo $this->Html->css('resources-browse', ['block' => true]);
?>
<div class="resources-browse">
    <!-- Header -->
    <div class="page-header">
        <h1><?= $this->ContentBlock->text('resources-library-title') ?></h1>
        <p><?= $this->ContentBlock->text('resources-library-intro-text') ?></p>
    </div>

    <!-- Filters -->
    <div class="resource-filters">
        <?php $currentType = $this->request->getQuery('type'); ?>

        <?= $this->Html->link(
            'All Resources',
            ['action' => 'browse'],
            ['class' => 'filter-btn' . (!$currentType ? ' active' : '')]
        ) ?>
        <?= $this->Html->link(
            'Articles',
            ['action' => 'browse', '?' => ['type' => 'article']],
            ['class' => 'filter-btn' . ($currentType === 'article' ? ' active' : '')]
        ) ?>
        <?= $this->Html->link(
            'Videos',
            ['action' => 'browse', '?' => ['type' => 'video']],
            ['class' => 'filter-btn' . ($currentType === 'video' ? ' active' : '')]
        ) ?>
        <?= $this->Html->link(
            'Blog Posts',
            ['action' => 'browse', '?' => ['type' => 'blog']],
            ['class' => 'filter-btn' . ($currentType === 'blog' ? ' active' : '')]
        ) ?>
        <?= $this->Html->link(
            'News',
            ['action' => 'browse', '?' => ['type' => 'news']],
            ['class' => 'filter-btn' . ($currentType === 'news' ? ' active' : '')]
        ) ?>
        <?= $this->Html->link(
            'Links',
            ['action' => 'browse', '?' => ['type' => 'link']],
            ['class' => 'filter-btn' . ($currentType === 'link' ? ' active' : '')]
        ) ?>
    </div>

    <!-- Resources Grid -->
    <?php if (!empty($resources) && count($resources) > 0): ?>
        <div class="resources-grid">
            <?php foreach ($resources as $resource): ?>
                <div class="resource-card">
                    <?php if ($resource->thumbnail_url): ?>
                        <div class="resource-thumbnail">
                            <img src="<?= h($resource->thumbnail_url) ?>" alt="<?= h($resource->title) ?>">
                            <span class="type-badge type-<?= h($resource->type) ?>">
                                <?= h(ucfirst($resource->type)) ?>
                            </span>
                        </div>
                    <?php else: ?>
                        <div class="resource-thumbnail no-image">
                            <span class="type-badge type-<?= h($resource->type) ?>">
                                <?= h(ucfirst($resource->type)) ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="resource-body">
                        <h3><?= h($resource->title) ?></h3>

                        <?php if ($resource->body): ?>
                            <p class="excerpt">
                                <?= $this->Text->truncate(strip_tags($resource->body), 150, ['ellipsis' => '...', 'exact' => false]) ?>
                            </p>
                        <?php endif; ?>

                        <div class="resource-meta">
                            <?php if ($resource->published_at): ?>
                                <span><?= $resource->published_at->format('M d, Y') ?></span>
                            <?php endif; ?>
                            <?php if (isset($resource->clinician->user)): ?>
                                <span>By <?= h($resource->clinician->user->first_name . ' ' . $resource->clinician->user->last_name) ?></span>
                            <?php endif; ?>
                        </div>

                        <?= $this->Html->link(
                            'Read More',
                            ['action' => 'read', $resource->id],
                            ['class' => 'read-link']
                        ) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination-container">
            <?= $this->Paginator->prev('‹ Previous') ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next('Next ›') ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <p><?= $this->ContentBlock->text('resources-empty-text') ?></p>
        </div>
    <?php endif; ?>
</div>
