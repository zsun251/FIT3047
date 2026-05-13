<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Resource> $resources
 */

use App\Policy\UserAccess;

$this->assign('title', 'Manage Resources');
echo $this->Html->css('resources-staff', ['block' => true]);

$identity    = $this->request->getAttribute('identity');
$isAdmin     = $identity && UserAccess::isAdmin($identity);
$myClinicianId = $isAdmin ? null : UserAccess::getClinicianId($identity);

$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
    ['title' => 'Resources', 'url' => null],
];
?>

<?= $this->element('page_header', ['breadcrumbs' => $breadcrumbs]) ?>

<!-- Page Header -->
<div class="resource-page-header">
    <div>
        <h1>Resource Library</h1>
        <p>Manage all published educational resources</p>
    </div>
    <?= $this->Html->link(
        '<i class="bi bi-plus-circle"></i> Add Resource',
        ['action' => 'add'],
        ['class' => 'btn btn-primary', 'escape' => false]
    ) ?>
</div>

<!-- Type Filters -->
<div class="resource-filters">
    <?php $currentType = $this->request->getQuery('type'); ?>
    <?= $this->Html->link('All', ['action' => 'index'], ['class' => 'filter-btn' . (!$currentType ? ' active' : '')]) ?>
    <?= $this->Html->link('Articles',   ['action' => 'index', '?' => ['type' => 'article']], ['class' => 'filter-btn' . ($currentType === 'article' ? ' active' : '')]) ?>
    <?= $this->Html->link('Videos',     ['action' => 'index', '?' => ['type' => 'video']],   ['class' => 'filter-btn' . ($currentType === 'video'   ? ' active' : '')]) ?>
    <?= $this->Html->link('Blog Posts', ['action' => 'index', '?' => ['type' => 'blog']],    ['class' => 'filter-btn' . ($currentType === 'blog'    ? ' active' : '')]) ?>
    <?= $this->Html->link('News',       ['action' => 'index', '?' => ['type' => 'news']],    ['class' => 'filter-btn' . ($currentType === 'news'    ? ' active' : '')]) ?>
    <?= $this->Html->link('Links',      ['action' => 'index', '?' => ['type' => 'link']],    ['class' => 'filter-btn' . ($currentType === 'link'    ? ' active' : '')]) ?>
</div>

<!-- Resources Grid -->
<?php if (!empty($resources) && count($resources) > 0): ?>
    <div class="resources-grid">
        <?php foreach ($resources as $resource): ?>
            <div class="resource-card">

                <!-- Thumbnail -->
                <div class="resource-thumbnail <?= $resource->thumbnail_url ? '' : 'no-image' ?>">
                    <?php if ($resource->thumbnail_url): ?>
                        <img src="<?= h($resource->thumbnail_url) ?>" alt="<?= h($resource->title) ?>">
                    <?php else: ?>
                        <?php
                        $icons = ['article' => 'bi-file-text', 'video' => 'bi-play-circle', 'blog' => 'bi-journal-text', 'news' => 'bi-newspaper', 'link' => 'bi-link-45deg'];
                        $icon = $icons[$resource->type] ?? 'bi-file-earmark';
                        ?>
                        <i class="bi <?= $icon ?> placeholder-icon"></i>
                    <?php endif; ?>

                    <span class="type-badge type-<?= h($resource->type) ?>">
                        <?= h(ucfirst($resource->type)) ?>
                    </span>

                    <span class="status-badge status-<?= h($resource->status) ?>">
                        <?= h(ucfirst($resource->status)) ?>
                    </span>
                </div>

                <!-- Body -->
                <div class="resource-card-body">
                    <h3 class="resource-card-title"><?= h($resource->title) ?></h3>

                    <?php if ($resource->body): ?>
                        <p class="resource-card-excerpt">
                            <?= h($this->Text->truncate(strip_tags($resource->body), 120, ['ellipsis' => '...', 'exact' => false])) ?>
                        </p>
                    <?php else: ?>
                        <p class="resource-card-excerpt text-muted fst-italic">No description provided.</p>
                    <?php endif; ?>

                    <!-- Meta -->
                    <div class="resource-card-meta">
                        <?php if ($resource->published_at): ?>
                            <span>
                                <i class="bi bi-calendar3"></i>
                                <?= $resource->published_at->format('M d, Y') ?>
                            </span>
                        <?php endif; ?>
                        <?php if (isset($resource->clinician->user)): ?>
                            <span>
                                <i class="bi bi-person-circle"></i>
                                <?= h($resource->clinician->user->first_name . ' ' . $resource->clinician->user->last_name) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Actions -->
                    <?php $canModify = $isAdmin || $resource->clinician_id === $myClinicianId; ?>
                    <div class="resource-card-actions">
                        <?php if ($canModify): ?>
                            <?= $this->Html->link(
                                '<i class="bi bi-eye"></i> View',
                                ['action' => 'view', $resource->id],
                                ['class' => 'btn btn-outline-primary btn-sm', 'escape' => false]
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="bi bi-pencil"></i> Edit',
                                ['action' => 'edit', $resource->id],
                                ['class' => 'btn btn-outline-secondary btn-sm', 'escape' => false]
                            ) ?>
                            <?= $this->Form->postLink(
                                '<i class="bi bi-trash"></i>',
                                ['action' => 'delete', $resource->id],
                                [
                                    'method'  => 'delete',
                                    'confirm' => __('Delete "{0}"? This cannot be undone.', $resource->title),
                                    'class'   => 'btn btn-outline-danger btn-sm',
                                    'escape'  => false,
                                    'title'   => 'Delete',
                                    'style'   => 'flex: 0;',
                                    'block'   => true,
                                ]
                            ) ?>
                        <?php else: ?>
                            <span class="text-muted" style="font-size:.8125rem; padding:.375rem 0;">
                                <i class="bi bi-lock"></i> Published by another clinician
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
    <?= $this->fetch('postLink') ?>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        <?= $this->Paginator->prev('‹') ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next('›') ?>
    </div>

<?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon"><i class="bi bi-journal-x"></i></div>
        <h3>No resources yet</h3>
        <p>Create your first resource to share with patients.</p>
        <?= $this->Html->link(
            '<i class="bi bi-plus-circle"></i> Add Resource',
            ['action' => 'add'],
            ['class' => 'btn btn-primary', 'escape' => false]
        ) ?>
    </div>
<?php endif; ?>
