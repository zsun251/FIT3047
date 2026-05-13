<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Resource $resource
 */

$this->assign('title', 'Edit Resource');
echo $this->Html->css('form-shared', ['block' => true]);
echo $this->Html->css('resources-staff', ['block' => true]);

$statusClass = $resource->status === 'published' ? 'bg-success' : 'bg-secondary';

$breadcrumbs = [
    ['title' => 'Dashboard',  'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
    ['title' => 'Resources',  'url' => ['controller' => 'Resources',  'action' => 'index']],
    ['title' => h($resource->title), 'url' => ['controller' => 'Resources', 'action' => 'view', $resource->id]],
    ['title' => 'Edit', 'url' => null],
];
?>

<?= $this->element('page_header', ['breadcrumbs' => $breadcrumbs]) ?>

<div class="form-container">

    <!-- Form Header -->
    <div class="form-header">
        <div>
            <h1><i class="bi bi-pencil-square" style="color:#0d6efd;margin-right:.5rem;"></i>Edit Resource</h1>
            <small class="text-muted">ID #<?= $resource->id ?> &mdash; Last updated: <?= $resource->updated_at->format('M d, Y H:i') ?></small>
        </div>
        <span class="badge <?= $statusClass ?>"><?= h(ucfirst($resource->status)) ?></span>
    </div>

    <!-- Read-only author info -->
    <?php if (isset($resource->clinician->user)): ?>
        <div class="resource-form-info">
            <i class="bi bi-person-circle"></i>
            <span>
                Author: <strong><?= h($resource->clinician->user->first_name . ' ' . $resource->clinician->user->last_name) ?></strong>
                <?php if ($resource->published_at): ?>
                    &mdash; Published <?= $resource->published_at->format('F j, Y') ?>
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>

    <?php if ($resource->getErrors()): ?>
        <div class="alert alert-danger mb-4" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill"></i> Could not save — please fix the following:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($resource->getErrors() as $field => $errors): ?>
                    <?php foreach ((array)$errors as $msg): ?>
                        <li><strong><?= h(ucwords(str_replace('_', ' ', $field))) ?>:</strong> <?= h($msg) ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?= $this->Form->create($resource) ?>

    <!-- Basic Information -->
    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-card-text"></i> Basic Information
        </h2>
        <div class="form-grid">

            <div class="form-group form-grid-full">
                <?= $this->Form->control('title', [
                    'label'     => 'Title',
                    'required'  => true,
                    'maxlength' => 255,
                    'class'     => 'form-control',
                    'id'        => 'resource-title',
                ]) ?>
            </div>

            <div class="form-group">
                <?= $this->Form->control('type', [
                    'label'    => 'Resource Type',
                    'type'     => 'select',
                    'required' => true,
                    'class'    => 'form-select',
                    'options'  => [
                        'article' => 'Article',
                        'video'   => 'Video',
                        'blog'    => 'Blog Post',
                        'link'    => 'External Link',
                        'news'    => 'News',
                    ],
                    'id' => 'resource-type',
                ]) ?>
            </div>

            <div class="form-group">
                <?= $this->Form->control('status', [
                    'label'   => 'Status',
                    'type'    => 'select',
                    'class'   => 'form-select',
                    'options' => [
                        'published' => 'Published — visible to patients',
                        'draft'     => 'Draft — hidden from patients',
                    ],
                ]) ?>
            </div>

        </div>
    </div>

    <!-- Content -->
    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-text-paragraph"></i> Content
        </h2>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <?= $this->Form->control('body', [
                'label'       => 'Body / Description',
                'type'        => 'textarea',
                'rows'        => 10,
                'class'       => 'form-control',
                'placeholder' => 'Write the main content of this resource here.',
                'id'          => 'resource-body',
            ]) ?>
            <div class="char-counter" id="body-counter">0 characters</div>
        </div>

    </div>

    <!-- Media & Links -->
    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-link-45deg"></i> Media &amp; Links
        </h2>
        <div class="form-grid">

            <div class="form-group form-grid-full">
                <?= $this->Form->control('url', [
                    'label'       => 'External URL',
                    'type'        => 'text',
                    'class'       => 'form-control',
                    'placeholder' => 'https://example.com/resource',
                    'id'          => 'resource-url',
                ]) ?>
                <div class="form-text">
                    <i class="bi bi-info-circle"></i>
                    For <strong>Video</strong>: paste a YouTube URL to embed automatically.
                    For <strong>Link</strong>: the URL patients will visit.
                </div>
            </div>

            <div class="form-group form-grid-full">
                <?= $this->Form->control('thumbnail_url', [
                    'label'       => 'Thumbnail Image URL',
                    'type'        => 'text',
                    'class'       => 'form-control',
                    'placeholder' => 'https://example.com/image.jpg',
                    'id'          => 'resource-thumbnail',
                ]) ?>
                <div class="form-text"><i class="bi bi-image"></i> Optional cover image. Must start with https://</div>

                <!-- Live image preview -->
                <div id="thumbnail-preview-wrap" style="display:none; margin-top:.75rem;">
                    <p style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#868e96;margin-bottom:.5rem;">Preview</p>
                    <img id="thumbnail-preview-img"
                         src=""
                         alt="Thumbnail preview"
                         style="max-height:180px; max-width:100%; border-radius:.375rem; border:1px solid #e9ecef; object-fit:cover;"
                         onerror="this.parentElement.style.display='none'">
                </div>
            </div>

        </div>
    </div>

    <!-- Form Actions -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Save Changes
        </button>
        <?= $this->Html->link(
            '<i class="bi bi-arrow-left"></i> Back to Resource',
            ['action' => 'view', $resource->id],
            ['class' => 'btn btn-outline-secondary', 'escape' => false]
        ) ?>
        <?= $this->Html->link(
            '<i class="bi bi-list-ul"></i> All Resources',
            ['action' => 'index'],
            ['class' => 'btn btn-outline-secondary', 'escape' => false]
        ) ?>
        <?= $this->Form->postLink(
            '<i class="bi bi-trash"></i> Delete',
            ['action' => 'delete', $resource->id],
            [
                'method'  => 'delete',
                'confirm' => __('Delete "{0}"? This cannot be undone.', $resource->title),
                'class'   => 'btn btn-outline-danger ms-auto',
                'escape'  => false,
                'block'   => true,
            ]
        ) ?>
    </div>

    <?= $this->Form->end() ?>
    <?= $this->fetch('postLink') ?>
</div>

<?= $this->Html->scriptBlock(<<<JS
    // Character counter for body
    const bodyEl = document.getElementById('resource-body');
    const counter = document.getElementById('body-counter');
    if (bodyEl && counter) {
        const update = () => {
            const n = bodyEl.value.length;
            counter.textContent = n.toLocaleString() + ' character' + (n !== 1 ? 's' : '');
        };
        bodyEl.addEventListener('input', update);
        update();
    }

    // Live image preview for thumbnail URL
    const thumbEl  = document.getElementById('resource-thumbnail');
    const prevWrap = document.getElementById('thumbnail-preview-wrap');
    const prevImg  = document.getElementById('thumbnail-preview-img');

    function updateThumbPreview() {
        const val = thumbEl ? thumbEl.value.trim() : '';
        if (val && prevWrap && prevImg) {
            prevImg.src = val;
            prevWrap.style.display = 'block';
        } else if (prevWrap) {
            prevWrap.style.display = 'none';
        }
    }

    if (thumbEl) {
        thumbEl.addEventListener('input', updateThumbPreview);
        updateThumbPreview(); // show existing value on page load
    }
JS, ['block' => 'script']) ?>
