/**
 * Content block edit page interactions.
 *
 * Handles live validation, image previews, image file labels, textarea sizing,
 * and Bootstrap Icons picker behaviour.
 */
document.addEventListener('DOMContentLoaded', () => {
    const saveButton = document.querySelector('.content-block-edit-form button[type="submit"]');

    const validationState = {
        text: true,
        image: true,
    };

    const defaultIcons = [
        'bi-heart-pulse',
        'bi-bell',
        'bi-clipboard2-pulse',
        'bi-person-lines-fill',
        'bi-house',
        'bi-house-door',
        'bi-grid',
        'bi-journal-medical',
        'bi-journal-text',
        'bi-people',
        'bi-person',
        'bi-person-check',
        'bi-calendar3',
        'bi-calendar-event',
        'bi-card-text',
        'bi-envelope',
        'bi-shield-lock',
        'bi-speedometer2',
        'bi-globe2',
        'bi-info-circle',
        'bi-question-circle',
        'bi-check-circle',
        'bi-lightbulb',
        'bi-chat-dots',
        'bi-activity',
    ];

    const updateSaveButton = () => {
        if (!saveButton) {
            return;
        }

        saveButton.disabled = !validationState.text || !validationState.image;
    };

    const getBootstrapIconsFromStylesheets = () => {
        const icons = new Set(defaultIcons);

        Array.from(document.styleSheets).forEach((stylesheet) => {
            try {
                Array.from(stylesheet.cssRules || []).forEach((rule) => {
                    const selector = rule.selectorText || '';
                    const matches = selector.matchAll(/\.bi-([a-z0-9-]+)::before/g);

                    Array.from(matches).forEach((match) => {
                        icons.add(`bi-${match[1]}`);
                    });
                });
            } catch (error) {
                // Some external stylesheets cannot be read by the browser.
            }
        });

        return Array.from(icons).sort();
    };

    const setupTextareaSizing = () => {
        const textarea = document.querySelector('[data-auto-grow-text="1"]');

        if (!textarea) {
            return;
        }

        const updateTextareaSize = () => {
            textarea.classList.toggle('is-long-text', textarea.value.length > 100);
        };

        textarea.addEventListener('input', updateTextareaSize);
        updateTextareaSize();
    };

    const setupTextFeedback = () => {
        const input = document.getElementById('content-block-value');
        const counter = document.getElementById('content-block-character-count');
        const help = document.getElementById('content-block-character-help');
        const error = document.getElementById('content-block-character-error');

        if (!input || !counter || !help || !error) {
            return;
        }

        const maxLength = Number(input.dataset.maxLength);
        const iconMode = input.dataset.iconMode === '1';
        const iconPattern = /^bi-[a-z0-9-]{1,80}$/;

        const updateCharacterFeedback = () => {
            const currentLength = input.value.length;
            const value = input.value.trim();

            counter.textContent = currentLength.toString();

            input.classList.remove('is-invalid');
            help.classList.remove('is-invalid');
            error.classList.add('d-none');
            error.textContent = 'Character limit exceeded.';

            validationState.text = true;

            if (currentLength > maxLength) {
                input.classList.add('is-invalid');
                help.classList.add('is-invalid');
                error.classList.remove('d-none');

                validationState.text = false;
                updateSaveButton();

                return;
            }

            if (iconMode && value.length > 0 && !iconPattern.test(value)) {
                input.classList.add('is-invalid');
                help.classList.add('is-invalid');
                error.textContent = 'Invalid Bootstrap icon class.';
                error.classList.remove('d-none');

                validationState.text = false;
                updateSaveButton();

                return;
            }

            updateSaveButton();
        };

        input.addEventListener('input', updateCharacterFeedback);
        updateCharacterFeedback();
    };

    const setupIconPicker = () => {
        const input = document.getElementById('content-block-value');
        const previewIcon = document.getElementById('content-block-selected-icon');
        const searchInput = document.getElementById('content-block-icon-search');
        const grid = document.getElementById('content-block-icon-grid');
        const emptyMessage = document.getElementById('content-block-icon-empty');
        const iconPattern = /^bi-[a-z0-9-]{1,80}$/;

        if (!input || !previewIcon || !grid) {
            return;
        }

        const allIcons = getBootstrapIconsFromStylesheets();

        const updatePreview = () => {
            const value = input.value.trim();

            previewIcon.className = iconPattern.test(value) ? `bi ${value}` : 'bi bi-question-circle';

            grid.querySelectorAll('[data-content-block-icon-choice]').forEach((button) => {
                button.classList.toggle('active', button.dataset.contentBlockIconChoice === value);
            });
        };

        const renderIconButtons = (icons) => {
            grid.innerHTML = '';

            icons.forEach((iconClass) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'content-block-icon-option';
                button.dataset.contentBlockIconChoice = iconClass;
                button.title = iconClass;

                button.innerHTML = `
                    <i class="bi ${iconClass}"></i>
                    <span>${iconClass.replace('bi-', '')}</span>
                `;

                button.addEventListener('click', () => {
                    input.value = iconClass;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    updatePreview();
                });

                grid.appendChild(button);
            });

            if (emptyMessage) {
                emptyMessage.classList.toggle('d-none', icons.length > 0);
            }

            updatePreview();
        };

        const updateSearchResults = () => {
            const searchValue = searchInput ? searchInput.value.trim().toLowerCase() : '';
            const sourceIcons = searchValue === '' ? defaultIcons : allIcons;
            const filteredIcons = sourceIcons.filter((iconClass) => iconClass.toLowerCase().includes(searchValue));

            renderIconButtons(filteredIcons);
        };

        if (searchInput) {
            searchInput.addEventListener('input', updateSearchResults);
        }

        input.addEventListener('input', updatePreview);
        updateSearchResults();
    };

    const setupImageFeedback = () => {
        const input = document.getElementById('content-block-image');
        const help = document.getElementById('content-block-image-help');
        const error = document.getElementById('content-block-image-error');
        const fileName = document.getElementById('content-block-file-name');
        const previewCard = document.getElementById('content-block-new-image-preview-card');
        const previewImage = document.getElementById('content-block-new-image-preview');

        if (!input || !help || !error) {
            return;
        }

        const maxSize = Number(input.dataset.maxSize);
        const allowedTypes = input.dataset.allowedTypes.split(',');
        const allowedExtensions = input.dataset.allowedExtensions.split(',');

        let previewObjectUrl = null;

        const getExtension = (filename) => {
            const parts = filename.toLowerCase().split('.');

            return parts.length > 1 ? parts.pop() : '';
        };

        const resetImagePreview = () => {
            if (previewObjectUrl) {
                URL.revokeObjectURL(previewObjectUrl);
                previewObjectUrl = null;
            }

            if (previewCard) {
                previewCard.classList.add('d-none');
            }

            if (previewImage) {
                previewImage.src = '';
            }
        };

        const resetImageFeedback = () => {
            input.classList.remove('is-invalid');
            help.classList.remove('is-invalid');
            error.classList.add('d-none');
            error.textContent = '';

            resetImagePreview();
        };

        const updateImageFeedback = () => {
            const file = input.files[0];

            resetImageFeedback();
            validationState.image = true;

            if (!file) {
                if (fileName) {
                    fileName.textContent = 'No file selected.';
                }

                updateSaveButton();

                return;
            }

            if (fileName) {
                fileName.textContent = file.name;
            }

            const extension = getExtension(file.name);

            if (!allowedExtensions.includes(extension) || !allowedTypes.includes(file.type)) {
                input.classList.add('is-invalid');
                help.classList.add('is-invalid');
                error.textContent = 'Invalid image type.';
                error.classList.remove('d-none');

                validationState.image = false;
                updateSaveButton();

                return;
            }

            if (file.size > maxSize) {
                input.classList.add('is-invalid');
                help.classList.add('is-invalid');
                error.textContent = 'Image exceeds 2MB limit.';
                error.classList.remove('d-none');

                validationState.image = false;
                updateSaveButton();

                return;
            }

            if (previewCard && previewImage) {
                previewObjectUrl = URL.createObjectURL(file);
                previewImage.src = previewObjectUrl;
                previewCard.classList.remove('d-none');
            }

            updateSaveButton();
        };

        input.addEventListener('change', updateImageFeedback);
        updateImageFeedback();
    };

    setupTextareaSizing();
    setupTextFeedback();
    setupIconPicker();
    setupImageFeedback();
    updateSaveButton();
});
