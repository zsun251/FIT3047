/**
 * webroot/js/datatable-init.js
 *
 * DataTables initialization helper.
 * Provides a clean interface without the default ugly controls.
 */

/**
 * Initialize a DataTable with custom settings
 * @param {string} selector - CSS selector for the table
 * @param {object} options - Additional DataTables options to merge
 */
function initDataTable(selector, options = {}) {
    const defaults = {
        dom:
            "<'div.dt-header d-flex justify-content-between align-items-center mb-3'lf>" +
            "t" +
            "<'div.dt-footer d-flex justify-content-between align-items-center mt-3'ip>",

        renderer: 'bootstrap',
        lengthChange: true,
        pageLength: 10,
        searching: true,
        paging: true,
        info: true,
        ordering: true,
        responsive: true,
        autoWidth: false,
        language: {
            search: "",
            searchPlaceholder: "Search..."
        },
        order: [[0, 'desc']]
    };

    // Merge custom options with defaults
    const config = $.extend(true, {}, defaults, options);

    // Initialize DataTable
    const table = $(selector).DataTable(config);

    return table;
}

/**
 * Initialize mobile cards with search and pagination
 * @param {object} config - Configuration object
 */
function initMobileCards(config) {
    const {
        container,    // ID of cards container
        search,       // ID of search input
        info,         // ID of info element
        pagination,   // ID of pagination container
        empty,        // ID of empty message element
        perPage = 10  // Items per page
    } = config;

    const $container = $(container);
    const $search = $(search);
    const $info = $(info);
    const $pagination = $(pagination);
    const $empty = $(empty);

    let currentPage = 1;
    let filteredCards = [];

    // Get all cards
    function getAllCards() {
        return $container.find('.sub-card').toArray();
    }

    // Filter cards based on search
    function filterCards(searchTerm) {
        const allCards = getAllCards();
        const term = searchTerm.toLowerCase().trim();

        if (!term) {
            return allCards;
        }

        return allCards.filter(card => {
            const searchText = $(card).data('search') || '';
            return searchText.toLowerCase().includes(term);
        });
    }

    // Render current page
    function render() {
        const allCards = getAllCards();

        // Hide all cards first
        allCards.forEach(card => $(card).hide());

        // Show empty message if no results
        if (filteredCards.length === 0) {
            $empty.show();
            $info.text('');
            $pagination.empty();
            return;
        }

        $empty.hide();

        // Calculate pagination
        const totalPages = Math.ceil(filteredCards.length / perPage);
        const start = (currentPage - 1) * perPage;
        const end = Math.min(start + perPage, filteredCards.length);

        // Show cards for current page
        for (let i = start; i < end; i++) {
            $(filteredCards[i]).show();
        }

        // Update info text
        $info.text(`Showing ${start + 1}-${end} of ${filteredCards.length}`);

        // Render pagination
        renderPagination(totalPages);
    }

    // Render pagination buttons
    function renderPagination(totalPages) {
        $pagination.empty();

        if (totalPages <= 1) return;

        // Previous button
        const $prev = $('<button>')
            .addClass('cards-page-btn')
            .html('‹')
            .prop('disabled', currentPage === 1)
            .on('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    render();
                }
            });
        $pagination.append($prev);

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            // Show first, last, current, and pages around current
            if (
                i === 1 ||
                i === totalPages ||
                (i >= currentPage - 1 && i <= currentPage + 1)
            ) {
                const $btn = $('<button>')
                    .addClass('cards-page-btn')
                    .text(i)
                    .toggleClass('active', i === currentPage)
                    .on('click', () => {
                        currentPage = i;
                        render();
                    });
                $pagination.append($btn);
            } else if (
                i === currentPage - 2 ||
                i === currentPage + 2
            ) {
                // Add ellipsis
                $pagination.append('<span class="mx-1">…</span>');
            }
        }

        // Next button
        const $next = $('<button>')
            .addClass('cards-page-btn')
            .html('›')
            .prop('disabled', currentPage === totalPages)
            .on('click', () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    render();
                }
            });
        $pagination.append($next);
    }

    // Handle search input
    $search.on('input', function() {
        const searchTerm = $(this).val();
        filteredCards = filterCards(searchTerm);
        currentPage = 1; // Reset to first page
        render();
    });

    // Initial render
    filteredCards = getAllCards();
    render();
}
