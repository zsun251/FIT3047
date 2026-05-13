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
        <button type="button" class="btn btn-outline-info" onclick="openPreview()">
            <i class="bi bi-eye"></i> Preview
        </button>
        <?= $this->Html->link(
            '<i class="bi bi-x-circle"></i> Cancel',
            ['action' => 'index'],
            ['class' => 'btn btn-outline-secondary', 'escape' => false]
        ) ?>
    </div>

    <?= $this->Form->end() ?>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:#f8f9fa;border-bottom:1px solid #e9ecef;">
                <div>
                    <h5 class="modal-title" id="previewModalLabel">
                        <i class="bi bi-eye" style="color:#0d6efd;"></i> Patient Preview
                    </h5>
                    <small class="text-muted">This is how patients will see this resource. No changes have been saved.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="preview-modal-body" style="padding:2rem;min-height:300px;">
            </div>
            <div class="modal-footer" style="background:#f8f9fa;justify-content:space-between;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-arrow-left"></i> Back to Edit
                </button>
                <button type="button" class="btn btn-success" onclick="publishFromPreview()">
                    <i class="bi bi-check-circle"></i> Publish Resource
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->Html->scriptBlock(<<<'JS'
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

    // ── Preview modal ────────────────────────────────────────────────────────
    function esc(s) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(s));
        return d.innerHTML;
    }

    function openPreview() {
        const title = document.getElementById('resource-title')?.value.trim() || '';
        const type  = document.getElementById('resource-type')?.value || 'article';
        const body  = document.getElementById('resource-body')?.value.trim() || '';
        const url   = document.getElementById('resource-url')?.value.trim() || '';
        const thumb = document.getElementById('resource-thumbnail')?.value.trim() || '';

        const typeConfig = {
            article: { icon: 'bi-file-text',       label: 'Article',           color: 'type-article' },
            video:   { icon: 'bi-play-circle-fill', label: 'Video',             color: 'type-video'   },
            blog:    { icon: 'bi-journal-text',     label: 'Blog Post',         color: 'type-blog'    },
            news:    { icon: 'bi-newspaper',        label: 'News',              color: 'type-news'    },
            link:    { icon: 'bi-link-45deg',       label: 'External Resource', color: 'type-link'    },
        };
        const cfg = typeConfig[type] || typeConfig.article;

        // Thumbnail
        const thumbHtml = thumb
            ? `<div style="margin:0 auto 2rem;">
                   <img src="${esc(thumb)}" alt="${esc(title)}"
                        style="width:100%;max-height:400px;object-fit:cover;border-radius:.5rem;border:1px solid #e9ecef;">
               </div>`
            : '';

        // Media (video embed or external link box)
        let mediaHtml = '';
        if (type === 'video' && url) {
            const yt = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\&\?\/]+)/);
            mediaHtml = yt
                ? `<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:.5rem;margin-bottom:1.5rem;">
                       <iframe src="https://www.youtube.com/embed/${esc(yt[1])}"
                               style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;"
                               allowfullscreen></iframe>
                   </div>`
                : `<a href="${esc(url)}" target="_blank" style="color:#0d6efd;">
                       <i class="bi bi-box-arrow-up-right"></i> ${esc(url)}
                   </a>`;
        } else if (type === 'link' && url) {
            mediaHtml = `<div style="border:1px solid #e9ecef;border-radius:.5rem;padding:1.25rem;display:flex;gap:1rem;align-items:center;margin-bottom:1.5rem;background:#f8f9fa;">
                <i class="bi bi-box-arrow-up-right" style="font-size:1.5rem;color:#0d6efd;flex-shrink:0;"></i>
                <div>
                    <div style="font-weight:600;margin-bottom:.25rem;">Visit External Resource</div>
                    <div style="font-size:.875rem;color:#6c757d;margin-bottom:.75rem;">This resource is hosted on an external website.</div>
                    <a href="${esc(url)}" target="_blank" class="btn btn-sm btn-primary">
                        Continue to Resource <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>`;
        }

        // Reading time + body paragraphs
        let readTimeBadge = '';
        let bodyHtml = '';
        if (body) {
            const words = body.trim().split(/\s+/).filter(Boolean).length;
            readTimeBadge = `<span><i class="bi bi-clock"></i> ${Math.max(1, Math.ceil(words / 200))} min read</span>`;
            bodyHtml = body.split(/\n\n+/).filter(p => p.trim())
                .map(p => `<p style="margin-bottom:1rem;">${esc(p).replace(/\n/g, '<br>')}</p>`)
                .join('');
        }

        const today = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

        document.getElementById('preview-modal-body').innerHTML = `
            <div style="max-width:800px;margin:0 auto;font-family:inherit;">
                <div style="margin-bottom:1rem;">
                    <span class="type-badge ${cfg.color}"
                          style="position:static;display:inline-flex;align-items:center;gap:.35rem;
                                 padding:.3rem .75rem;border-radius:2rem;font-size:.75rem;font-weight:600;">
                        <i class="bi ${cfg.icon}"></i> ${cfg.label}
                    </span>
                    <span class="badge bg-warning text-dark ms-2" style="font-size:.7rem;vertical-align:middle;">Preview</span>
                </div>
                <h1 style="font-size:2rem;font-weight:700;margin:0 0 1rem;line-height:1.3;color:#1f2937;">
                    ${title ? esc(title) : '<em style="color:#9ca3af;font-weight:400;">No title entered</em>'}
                </h1>
                <div style="display:flex;gap:1.25rem;flex-wrap:wrap;font-size:.875rem;color:#6c757d;margin-bottom:1.5rem;">
                    <span><i class="bi bi-calendar3"></i> ${today}</span>
                    ${readTimeBadge}
                </div>
                ${thumbHtml}
                ${mediaHtml}
                ${bodyHtml
                    ? `<div style="line-height:1.8;font-size:1rem;color:#374151;">${bodyHtml}</div>`
                    : '<p style="color:#9ca3af;font-style:italic;">No content added yet.</p>'}
            </div>`;

        bootstrap.Modal.getOrCreateInstance(document.getElementById('previewModal')).show();
    }

    function publishFromPreview() {
        bootstrap.Modal.getInstance(document.getElementById('previewModal'))?.hide();
        document.querySelector('form').submit();
    }
JS, ['block' => 'script']) ?>
