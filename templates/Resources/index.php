<?php
declare(strict_types=1);

/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Resource>|array $resources
 */

use App\Policy\UserAccess;

$this->assign('title', __('Manage Resources'));
echo $this->Html->css('resources-staff', ['block' => true]);

$identity = $this->request->getAttribute('identity');
$isAdmin = $identity && UserAccess::isAdmin($identity);
$myClinicianId = $isAdmin ? null : UserAccess::getClinicianId($identity);

$resources = $resources ?? [];
$resourceList = is_array($resources) ? $resources : iterator_to_array($resources);

$currentType = (string)($this->request->getQuery('type') ?? '');

$allowedTypes = ['article', 'video', 'blog', 'news', 'link'];
$currentType = in_array($currentType, $allowedTypes, true) ? $currentType : '';

$backUrl = $this->request->referer(true);

if (!$backUrl || str_contains($backUrl, '/resources')) {
    $backUrl = ['controller' => 'Dashboard', 'action' => 'staff'];
}

$getResourceType = function ($resource) use ($allowedTypes): string {
    $type = strtolower(trim((string)($resource->type ?? '')));

    return in_array($type, $allowedTypes, true) ? $type : 'article';
};

$getResourceStatus = function ($resource): string {
    $status = strtolower(trim((string)($resource->status ?? '')));

    return in_array($status, ['published', 'draft'], true) ? $status : 'draft';
};

$getResourceIcon = function (string $type): string {
    return match ($type) {
        'article' => 'bi-file-text',
        'video' => 'bi-play-circle',
        'blog' => 'bi-journal-text',
        'news' => 'bi-newspaper',
        'link' => 'bi-link-45deg',
        default => 'bi-file-earmark',
    };
};

$getResourceTypeLabel = function (string $type): string {
    return match ($type) {
        'article' => __('Article'),
        'video' => __('Video'),
        'blog' => __('Blog'),
        'news' => __('News'),
        'link' => __('Link'),
        default => __('Resource'),
    };
};

$getClinicianName = function ($resource): ?string {
    if (empty($resource->clinician->user)) {
        return null;
    }

    $firstName = trim((string)($resource->clinician->user->first_name ?? ''));
    $lastName = trim((string)($resource->clinician->user->last_name ?? ''));
    $fullName = trim($firstName . ' ' . $lastName);

    return $fullName !== '' ? $fullName : null;
};
?>

<div class="resources-staff-page">

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
                            'aria-label' => __('Return to dashboard'),
                        ],
                    ) ?>

                    <h1 class="rc-dashboard-title"><?= __('Resource Library') ?></h1>
                </div>

                <p class="rc-dashboard-subtitle">
                    <?= __('Manage all published educational resources') ?>
                </p>
            </div>

            <?= $this->Html->link(
                '<i class="bi bi-plus-circle"></i><span>' . __('Add Resource') . '</span>',
                ['action' => 'add'],
                [
                    'class' => 'rc-action-pill resources-add-button',
                    'escape' => false,
                ],
            ) ?>
        </div>
    </div>

    <div class="resource-filters">
        <?= $this->Html->link(
            __('All'),
            ['action' => 'index'],
            [
                'class' => 'rc-pill-control rc-pill-control-blue resource-filter-button' . (!$currentType ? ' is-active' : ''),
            ],
        ) ?>

        <?= $this->Html->link(
            __('Articles'),
            ['action' => 'index', '?' => ['type' => 'article']],
            [
                'class' => 'rc-pill-control rc-pill-control-blue resource-filter-button' . ($currentType === 'article' ? ' is-active' : ''),
            ],
        ) ?>

        <?= $this->Html->link(
            __('Videos'),
            ['action' => 'index', '?' => ['type' => 'video']],
            [
                'class' => 'rc-pill-control rc-pill-control-blue resource-filter-button' . ($currentType === 'video' ? ' is-active' : ''),
            ],
        ) ?>

        <?= $this->Html->link(
            __('Blog Posts'),
            ['action' => 'index', '?' => ['type' => 'blog']],
            [
                'class' => 'rc-pill-control rc-pill-control-blue resource-filter-button' . ($currentType === 'blog' ? ' is-active' : ''),
            ],
        ) ?>

        <?= $this->Html->link(
            __('News'),
            ['action' => 'index', '?' => ['type' => 'news']],
            [
                'class' => 'rc-pill-control rc-pill-control-blue resource-filter-button' . ($currentType === 'news' ? ' is-active' : ''),
            ],
        ) ?>

        <?= $this->Html->link(
            __('Links'),
            ['action' => 'index', '?' => ['type' => 'link']],
            [
                'class' => 'rc-pill-control rc-pill-control-blue resource-filter-button' . ($currentType === 'link' ? ' is-active' : ''),
            ],
        ) ?>
    </div>

    <?php if (!empty($resourceList)): ?>
        <div class="resources-grid">
            <?php foreach ($resourceList as $resource): ?>
                <?php
                $resourceType = $getResourceType($resource);
                $resourceStatus = $getResourceStatus($resource);
                $resourceIcon = $getResourceIcon($resourceType);
                $clinicianName = $getClinicianName($resource);

                $canModify = $isAdmin
                    || (
                        $myClinicianId !== null
                        && (int)($resource->clinician_id ?? 0) === (int)$myClinicianId
                    );
                ?>

                <article class="resource-card">
                    <div class="resource-thumbnail <?= !empty($resource->thumbnail_url) ? '' : 'no-image' ?>">
                        <?php if (!empty($resource->thumbnail_url)): ?>
                            <img
                                src="<?= h($resource->thumbnail_url) ?>"
                                alt="<?= h($resource->title) ?>"
                            >
                        <?php else: ?>
                            <i class="bi <?= h($resourceIcon) ?> placeholder-icon"></i>
                        <?php endif; ?>

                        <span class="resource-type-badge resource-type-<?= h($resourceType) ?>">
                            <?= h($getResourceTypeLabel($resourceType)) ?>
                        </span>

                        <span class="resource-status-badge resource-status-<?= h($resourceStatus) ?>">
                            <?= h(ucfirst($resourceStatus)) ?>
                        </span>
                    </div>

                    <div class="resource-card-body">
                        <h3 class="resource-card-title">
                            <?= h($resource->title) ?>
                        </h3>

                        <?php if (!empty($resource->body)): ?>
                            <p class="resource-card-excerpt">
                                <?= h($this->Text->truncate(strip_tags((string)$resource->body), 120, [
                                    'ellipsis' => '...',
                                    'exact' => false,
                                ])) ?>
                            </p>
                        <?php else: ?>
                            <p class="resource-card-excerpt text-muted fst-italic">
                                <?= __('No description provided.') ?>
                            </p>
                        <?php endif; ?>

                        <div class="resource-card-meta">
                            <?php if (!empty($resource->published_at)): ?>
                                <span>
                                    <i class="bi bi-calendar3"></i>
                                    <?= h($resource->published_at->format('M d, Y')) ?>
                                </span>
                            <?php endif; ?>

                            <?php if ($clinicianName): ?>
                                <span>
                                    <i class="bi bi-person-circle"></i>
                                    <?= h($clinicianName) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="resource-card-actions">
                            <?php if ($canModify): ?>
                                <?= $this->Html->link(
                                    '<i class="bi bi-eye"></i><span>' . __('View') . '</span>',
                                    ['action' => 'view', $resource->id],
                                    [
                                        'class' => 'rc-action-pill resource-card-action resource-card-action-view',
                                        'escape' => false,
                                    ],
                                ) ?>

                                <?= $this->Html->link(
                                    '<i class="bi bi-pencil"></i><span>' . __('Edit') . '</span>',
                                    ['action' => 'edit', $resource->id],
                                    [
                                        'class' => 'rc-action-pill rc-action-pill-green resource-card-action resource-card-action-edit',
                                        'escape' => false,
                                    ],
                                ) ?>

                                <?= $this->Form->postLink(
                                    '<i class="bi bi-trash"></i>',
                                    ['action' => 'delete', $resource->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Delete "{0}"? This cannot be undone.', $resource->title),
                                        'class' => 'rc-icon-action rc-icon-action-danger resource-card-delete',
                                        'escape' => false,
                                        'title' => __('Delete'),
                                        'aria-label' => __('Delete resource'),
                                        'block' => true,
                                    ],
                                ) ?>
                            <?php else: ?>
                                <span class="resource-card-locked-note">
                                    <i class="bi bi-lock"></i>
                                    <?= __('Published by another clinician') ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?= $this->fetch('postLink') ?>

        <div class="pagination-wrapper">
            <?= $this->Paginator->prev('‹') ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next('›') ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-journal-x"></i>
            </div>

            <h3><?= __('No resources yet') ?></h3>
            <p><?= __('Create your first resource to share with patients.') ?></p>

            <?= $this->Html->link(
                '<i class="bi bi-plus-circle"></i><span>' . __('Add Resource') . '</span>',
                ['action' => 'add'],
                [
                    'class' => 'rc-action-pill resources-add-button',
                    'escape' => false,
                ],
            ) ?>
        </div>
    <?php endif; ?>

</div>
