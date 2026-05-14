/**
 * Content block index page interactions.
 *
 * Handles section dropdowns and content type filtering outside the template.
 */
document.addEventListener('DOMContentLoaded', () => {
    const accordion = document.getElementById('contentBlocksAccordion');
    const filterButtons = document.querySelectorAll('[data-content-block-filter]');
    const emptyFilterState = document.getElementById('content-block-filter-empty');

    if (!accordion) {
        return;
    }

    const sectionToggles = accordion.querySelectorAll('[data-content-block-toggle]');
    const subsectionToggles = accordion.querySelectorAll('[data-content-block-subtoggle]');
    const sections = accordion.querySelectorAll('[data-content-block-section]');
    const subsections = accordion.querySelectorAll('[data-content-block-subsection]');
    const contentItems = accordion.querySelectorAll('[data-content-block-item]');

    let activeFilter = 'all';

    /**
     * Build the item count label used beside each section heading.
     */
    const formatCount = (count) => `${count} ${count === 1 ? 'item' : 'items'}`;

    /**
     * Count all currently visible content items inside a section.
     */
    const countVisibleItems = (container) => Array.from(container.querySelectorAll('[data-content-block-item]'))
        .filter((item) => !item.classList.contains('d-none')).length;

    /**
     * Update the item count label for a section or subsection.
     */
    const updateSectionCount = (container, count) => {
        const countTarget = container.querySelector('[data-content-block-count]');

        if (!countTarget) {
            return;
        }

        countTarget.textContent = formatCount(count);
    };

    /**
     * Close every top-level section except the one currently being opened.
     */
    const closeOtherSections = (activeToggle) => {
        sectionToggles.forEach((toggle) => {
            if (toggle === activeToggle) {
                return;
            }

            const sectionId = toggle.dataset.contentBlockToggle;
            const section = document.getElementById(sectionId);

            if (!section) {
                return;
            }

            section.classList.add('d-none');
            toggle.classList.add('collapsed');
            toggle.setAttribute('aria-expanded', 'false');
        });
    };

    /**
     * Toggle the requested top-level content section.
     */
    const toggleSection = (toggle) => {
        const sectionId = toggle.dataset.contentBlockToggle;
        const section = document.getElementById(sectionId);

        if (!section) {
            return;
        }

        const isOpening = section.classList.contains('d-none');

        closeOtherSections(toggle);

        section.classList.toggle('d-none', !isOpening);
        toggle.classList.toggle('collapsed', !isOpening);
        toggle.setAttribute('aria-expanded', isOpening ? 'true' : 'false');
    };

    /**
     * Toggle one of the nested home page subsections.
     */
    const toggleSubsection = (toggle) => {
        const sectionId = toggle.dataset.contentBlockSubtoggle;
        const section = document.getElementById(sectionId);

        if (!section) {
            return;
        }

        const isOpening = section.classList.contains('d-none');

        section.classList.toggle('d-none', !isOpening);
        toggle.classList.toggle('collapsed', !isOpening);
        toggle.setAttribute('aria-expanded', isOpening ? 'true' : 'false');
    };

    /**
     * Update which filter button is visually active.
     */
    const updateFilterButtons = () => {
        filterButtons.forEach((button) => {
            const isActive = button.dataset.contentBlockFilter === activeFilter;

            button.classList.toggle('active', isActive);
            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    };

    /**
     * Apply the selected type filter to every content item and section.
     */
    const applyFilter = () => {
        let visibleSectionCount = 0;

        contentItems.forEach((item) => {
            const itemType = item.dataset.contentBlockType;
            const shouldShow = activeFilter === 'all' || itemType === activeFilter;

            item.classList.toggle('d-none', !shouldShow);
        });

        subsections.forEach((subsection) => {
            const visibleItemCount = countVisibleItems(subsection);

            subsection.classList.toggle('d-none', visibleItemCount === 0);
            updateSectionCount(subsection, visibleItemCount);
        });

        sections.forEach((section) => {
            const visibleItemCount = countVisibleItems(section);

            section.classList.toggle('d-none', visibleItemCount === 0);
            updateSectionCount(section, visibleItemCount);

            if (visibleItemCount > 0) {
                visibleSectionCount++;
            }
        });

        if (emptyFilterState) {
            emptyFilterState.classList.toggle('d-none', visibleSectionCount > 0);
        }

        updateFilterButtons();
    };

    sectionToggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            toggleSection(toggle);
        });
    });

    subsectionToggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            toggleSubsection(toggle);
        });
    });

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            activeFilter = button.dataset.contentBlockFilter || 'all';
            applyFilter();
        });
    });

    applyFilter();
});
