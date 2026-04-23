<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Resource $resource
 */
$this->assign('title', $resource->title);

// CSS
echo $this->Html->css('resource-read', ['block' => true]);
?>

<!-- Back Link -->
<div class="back-navigation">
    <?= $this->Html->link(
        '<i class="bi bi-arrow-left"></i> Back to Library',
        ['action' => 'browse'],
        ['class' => 'back-link', 'escape' => false]
    ) ?>
</div>

<article class="resource-article">
    <!-- Article Header -->
    <header class="article-header">
        <!-- Type Badge -->
        <div class="article-type">
            <?php
            $typeConfig = [
                'article' => ['icon' => 'file-text', 'label' => 'Article', 'color' => 'blue'],
                'video' => ['icon' => 'play-circle-fill', 'label' => 'Video', 'color' => 'red'],
                'blog' => ['icon' => 'journal-text', 'label' => 'Blog Post', 'color' => 'green'],
                'news' => ['icon' => 'newspaper', 'label' => 'News', 'color' => 'orange'],
                'link' => ['icon' => 'link-45deg', 'label' => 'External Resource', 'color' => 'purple']
            ];
            $config = $typeConfig[$resource->type] ?? $typeConfig['article'];
            ?>
            <span class="type-badge type-<?= h($config['color']) ?>">
                <i class="bi bi-<?= h($config['icon']) ?>"></i>
                <?= h($config['label']) ?>
            </span>
        </div>

        <!-- Title -->
        <h1 class="article-title"><?= h($resource->title) ?></h1>

        <!-- Meta Information -->
        <div class="article-meta">
            <?php if ($resource->published_at): ?>
                <span class="meta-item">
                    <i class="bi bi-calendar3"></i>
                    <time datetime="<?= $resource->published_at->format('Y-m-d') ?>">
                        <?= $resource->published_at->format('F j, Y') ?>
                    </time>
                </span>
            <?php endif; ?>

            <?php if (isset($resource->clinician->user)): ?>
                <span class="meta-item">
                    <i class="bi bi-person-circle"></i>
                    <span class="author-name">
                        <?= h($resource->clinician->user->first_name . ' ' . $resource->clinician->user->last_name) ?>
                    </span>
                </span>
            <?php endif; ?>

            <?php
            // Estimate reading time (200 words per minute)
            if ($resource->body) {
                $wordCount = str_word_count(strip_tags($resource->body));
                $readingTime = max(1, ceil($wordCount / 200));
                ?>
                <span class="meta-item">
                    <i class="bi bi-clock"></i>
                    <?= $readingTime ?> min read
                </span>
            <?php } ?>
        </div>

        <!-- Featured Image -->
        <?php if ($resource->thumbnail_url): ?>
            <div class="article-featured-image">
                <img src="<?= h($resource->thumbnail_url) ?>" alt="<?= h($resource->title) ?>">
            </div>
        <?php endif; ?>
    </header>

    <!-- Article Content -->
    <div class="article-body">
        <?php if ($resource->type === 'video' && $resource->url): ?>
            <!-- Video Embed -->
            <div class="video-container">
                <?php
                // Try to embed YouTube videos
                if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $resource->url, $matches) ||
                    preg_match('/youtu\.be\/([^\&\?\/]+)/', $resource->url, $matches)) {
                    $videoId = $matches[1];
                    ?>
                    <iframe
                        src="https://www.youtube.com/embed/<?= h($videoId) ?>"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                <?php } else { ?>
                    <div class="video-link">
                        <i class="bi bi-play-circle-fill"></i>
                        <?= $this->Html->link(
                            'Watch Video',
                            $resource->url,
                            ['target' => '_blank', 'class' => 'btn-watch-video']
                        ) ?>
                    </div>
                <?php } ?>
            </div>
        <?php endif; ?>

        <?php if ($resource->body): ?>
            <!-- Main Content -->
            <div class="article-content">
                <?= $this->Text->autoParagraph(h($resource->body)) ?>
            </div>
        <?php endif; ?>

        <?php if ($resource->type === 'link' && $resource->url): ?>
            <!-- External Link -->
            <div class="external-link-box">
                <div class="external-link-icon">
                    <i class="bi bi-box-arrow-up-right"></i>
                </div>
                <div class="external-link-content">
                    <h3>Visit External Resource</h3>
                    <p>This resource is hosted on an external website.</p>
                    <?= $this->Html->link(
                        'Continue to Resource <i class="bi bi-arrow-right"></i>',
                        $resource->url,
                        [
                            'target' => '_blank',
                            'class' => 'btn-external',
                            'escape' => false
                        ]
                    ) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Article Footer -->
    <footer class="article-footer">
        <div class="footer-content">
            <div class="helpful-section">
                <h4>Was this helpful?</h4>
                <p>Let us know if you found this resource useful</p>
                <div class="helpful-buttons">
                    <button class="btn-helpful" onclick="alert('Thank you for your feedback!')">
                        <i class="bi bi-hand-thumbs-up"></i> Yes, helpful
                    </button>
                    <button class="btn-helpful" onclick="alert('Thank you for your feedback!')">
                        <i class="bi bi-hand-thumbs-down"></i> Not helpful
                    </button>
                </div>
            </div>

            <?php if ($resource->url && $resource->type !== 'link'): ?>
                <div class="source-section">
                    <h4>Original Source</h4>
                    <?= $this->Html->link(
                        $resource->url,
                        $resource->url,
                        ['target' => '_blank', 'class' => 'source-link']
                    ) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="back-to-library">
            <?= $this->Html->link(
                '<i class="bi bi-arrow-left"></i> Return to Resource Library',
                ['action' => 'browse'],
                ['class' => 'btn-back', 'escape' => false]
            ) ?>
        </div>
    </footer>
</article>


