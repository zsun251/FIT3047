/**
 * Content block edit page validation.
 *
 * Gives immediate visual feedback for text length and image upload rules.
 * Server-side validation in the controller still handles the real save protection.
 */
document.addEventListener('DOMContentLoaded', () => {
    const saveButton = document.querySelector('.contentBlocks form button[type="submit"]');

    const validationState = {
        text: true,
        image: true,
    };

    /**
     * Disable the save button when any active field has failed validation.
     */
    const updateSaveButton = () => {
        if (!saveButton) {
            return;
        }

        saveButton.disabled = !validationState.text || !validationState.image;
    };

    /**
     * Show the live character count for text blocks.
     */
    const setupTextFeedback = () => {
        const input = document.getElementById('content-block-value');
        const counter = document.getElementById('content-block-character-count');
        const help = document.getElementById('content-block-character-help');
        const error = document.getElementById('content-block-character-error');

        if (!input || !counter || !help || !error) {
            return;
        }

        const maxLength = Number(input.dataset.maxLength);

        const updateCharacterFeedback = () => {
            const currentLength = input.value.length;

            counter.textContent = currentLength.toString();

            input.classList.remove('is-invalid');
            help.classList.remove('is-invalid');
            error.classList.add('d-none');

            validationState.text = true;

            if (currentLength > maxLength) {
                input.classList.add('is-invalid');
                help.classList.add('is-invalid');
                error.classList.remove('d-none');

                validationState.text = false;
            }

            updateSaveButton();
        };

        input.addEventListener('input', updateCharacterFeedback);
        updateCharacterFeedback();
    };

    /**
     * Check image uploads and preview valid selected images before the form is submitted.
     */
    const setupImageFeedback = () => {
        const input = document.getElementById('content-block-image');
        const help = document.getElementById('content-block-image-help');
        const error = document.getElementById('content-block-image-error');
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

        /**
         * Hide the new-image preview and release any generated preview URL.
         */
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

        /**
         * Reset image field feedback before applying the current validation state.
         */
        const resetImageFeedback = () => {
            input.classList.remove('is-invalid');
            help.classList.remove('is-invalid');
            error.classList.add('d-none');
            error.textContent = '';

            resetImagePreview();
        };

        /**
         * Validate selected image type/size and show a preview when valid.
         */
        const updateImageFeedback = () => {
            const file = input.files[0];

            resetImageFeedback();
            validationState.image = true;

            if (!file) {
                updateSaveButton();

                return;
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

    setupTextFeedback();
    setupImageFeedback();
    updateSaveButton();
});
