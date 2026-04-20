<?php
// templates/Resources/browse.php
?>
<div class="resources-browse">
    <div class="page-header">
        <h1>Resource Library</h1>
        <p>Educational materials and helpful information about respiratory health</p>
    </div>

    <!-- Optional: Filter by type -->
    <div class="resource-filters">
        <?= $this->Html->link('All', ['action' => 'browse'], ['class' => 'filter-btn']) ?>
        <?= $this->Html->link('Articles', ['action' => 'browse', '?' => ['type' => 'article']], ['class' => 'filter-btn']) ?>
        <?= $this->Html->link('Videos', ['action' => 'browse', '?' => ['type' => 'video']], ['class' => 'filter-btn']) ?>
        <?= $this->Html->link('Blog Posts', ['action' => 'browse', '?' => ['type' => 'blog']], ['class' => 'filter-btn']) ?>
        <?= $this->Html->link('News', ['action' => 'browse', '?' => ['type' => 'news']], ['class' => 'filter-btn']) ?>
    </div>

    <div class="resources-grid">
        <?php foreach ($resources as $resource): ?>
            <div class="resource-card">
                <?php if ($resource->thumbnail_url): ?>
                    <div class="resource-thumbnail">
                        <img src="<?= h($resource->thumbnail_url) ?>" alt="<?= h($resource->title) ?>">
                    </div>
                <?php endif; ?>

                <div class="resource-content">
                    <span class="resource-type badge"><?= h($resource->type) ?></span>
                    <h3><?= h($resource->title) ?></h3>

                    <?php if ($resource->body): ?>
                        <p><?= $this->Text->truncate(strip_tags($resource->body), 150) ?></p>
                    <?php endif; ?>

                    <div class="resource-meta">
                        <span class="published-date">
                            <?= $resource->published_at ? $resource->published_at->format('M d, Y') : '' ?>
                        </span>
                        <?php if (isset($resource->clinician->user)): ?>
                            <span class="author">
                                By <?= h($resource->clinician->user->first_name . ' ' . $resource->clinician->user->last_name) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if ($resource->url): ?>
                        <?= $this->Html->link('Read More →', $resource->url, [
                            'target' => '_blank',
                            'class' => 'btn-read-more'
                        ]) ?>
                    <?php else: ?>
                        <?= $this->Html->link('Read More →', [
                            'action' => 'view',
                            $resource->id
                        ], ['class' => 'btn-read-more']) ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <div class="paginator">
        <?= $this->Paginator->prev('« Previous') ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next('Next »') ?>
    </div>
</div>
