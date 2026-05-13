<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Resource $resource
 */

$this->assign('title', 'Add Resource');
echo $this->Html->css('form-shared', ['block' => true]);
echo $this->Html->css('resources-staff', ['block' => true]);

$breadcrumbs = [
    ['title' => 'Dashboard',  'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
    ['title' => 'Resources',  'url' => ['controller' => 'Resources',  'action' => 'index']],
    ['title' => 'Add Resource', 'url' => null],
];
?>

<?= $this->element('page_header', ['breadcrumbs' => $breadcrumbs]) ?>

<div class="form-container">

    <!-- Form Header -->
    <div class="form-header">
        <div>
            <h1><i class="bi bi-journal-plus" style="color:#0d6efd;margin-right:.5rem;"></i>Add Resource</h1>
            <small class="text-muted">Create a new educational resource for patients</small>
        </div>
        <span class="badge bg-primary">New</span>
    </div>

    <div class="resource-form-info">
        <i class="bi bi-info-circle"></i>
        <?php if ($isAdmin): ?>
            <span>As an admin you can publish resources on behalf of any clinician. Select the author below — they will appear as the resource's creator in the library.</span>
        <?php else: ?>
            <span>Resources you publish will be visible to patients in the Resource Library. Your name will appear as the author automatically.</span>
        <?php endif; ?>
    </div>

    <?= $this->Form->create($resource) ?>

    <!-- Basic Information -->
    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-card-text"></i> Basic Information
        </h2>
        <div class="form-grid">

            <div class="form-group form-grid-full">
                <?= $this->Form->control('title', [
                    'label'       => 'Title',
                    'required'    => true,
                    'maxlength'   => 255,
                    'class'       => 'form-control',
                    'placeholder' => 'Enter a clear, descriptive title',
                    'id'          => 'resource-title',
                ]) ?>
            </div>

            <div class="form-group">
                <?= $this->Form->control('type', [
                    'label'   => 'Resource Type',
                    'type'    => 'select',
                    'required' => true,
                    'class'   => 'form-select',
                    'options' => [
                        'article' => 'Article',
                        'video'   => 'Video',
                        'blog'    => 'Blog Post',
                        'link'    => 'External Link',
                        'news'    => 'News',
                    ],
                    'empty'   => '— Select a type —',
                    'id'      => 'resource-type',
                ]) ?>
            </div>

            <?php if ($isAdmin && !empty($clinicians)): ?>
                <div class="form-group form-grid-full">
                    <?= $this->Form->control('clinician_id', [
                        'label'    => 'Author (Clinician)',
                        'type'     => 'select',
                        'required' => true,
                        'class'    => 'form-select',
                        'options'  => collection($clinicians)->combine(
                            function ($c) { return $c->id; },
                            function ($c) {
                                return $c->user->first_name . ' ' . $c->user->last_name;
                            }
                        )->toArray(),
                        'empty'    => '— Select an author —',
                    ]) ?>
                    <div class="form-text">
                        <i class="bi bi-shield-lock"></i>
                        Admin-only field — select which clinician this resource will be attributed to.
                    </div>
                </div>
            <?php endif; ?>

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
                'placeholder' => 'Write the main content of this resource here. For video or link types, you can provide a short summary.',
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
                    For <strong>Video</strong> type: paste a YouTube link to embed it automatically.
                    For <strong>Link</strong> type: this is the destination URL patients will visit.
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
            <i class="bi bi-check-circle"></i> Publish Resource
        </button>
        <?= $this->Html->link(
            '<i class="bi bi-x-circle"></i> Cancel',
            ['action' => 'index'],
            ['class' => 'btn btn-outline-secondary', 'escape' => false]
        ) ?>
    </div>

    <?= $this->Form->end() ?>
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
        updateThumbPreview();
    }
JS, ['block' => 'script']) ?>
