/**
 * Shared Mobile DataTable Functionality
 * Provides pagination and search for mobile card views
 */

class MobileDataTable {
    constructor(options = {}) {
        this.itemsPerPage = options.itemsPerPage || 10;
        this.currentPage = 1;
        this.activeFilter = options.defaultFilter || 'all';
        this.searchQuery = '';
        this.cardSelector = options.cardSelector || '.sub-card';
        this.filterAttribute = options.filterAttribute || 'data-status';
        this.searchAttribute = options.searchAttribute || 'data-search-text';
        this.paginationListSelector = options.paginationListSelector || '#mobilePaginationList';

        this.init();
    }

    init() {
        this.setupSearch();
        this.setupPagination();
        this.applyFilters();
    }

    setupSearch() {
        const self = this;

        $('#mobileSearch').on('keyup', function () {
            self.searchQuery = $(this).val().toLowerCase();
            self.currentPage = 1;
            self.applyFilters();
        });
    }

    setupPagination() {
        const self = this;

        $(document).on('click', this.paginationListSelector + ' .mobile-page-btn', function () {
            if ($(this).prop('disabled')) {
                return;
            }

            const targetPage = Number($(this).data('page'));

            if (!Number.isInteger(targetPage) || targetPage === self.currentPage) {
                return;
            }

            self.currentPage = targetPage;
            self.applyFilters();
            self.scrollToTop();
        });
    }

    setFilter(filter) {
        this.activeFilter = filter;
        this.currentPage = 1;
        this.applyFilters();
    }

    applyFilters() {
        const $cards = $(this.cardSelector);
        let visibleCards = $cards;

        if (this.activeFilter !== 'all') {
            visibleCards = visibleCards.filter('[' + this.filterAttribute + '="' + this.activeFilter + '"]');
        }

        if (this.searchQuery !== '') {
            const self = this;

            visibleCards = visibleCards.filter(function () {
                const searchText = $(this).attr(self.searchAttribute) || '';
                return searchText.indexOf(self.searchQuery) !== -1;
            });
        }

        $cards.hide();

        const totalItems = visibleCards.length;
        const totalPages = Math.max(1, Math.ceil(totalItems / this.itemsPerPage));

        if (this.currentPage > totalPages) {
            this.currentPage = totalPages;
        }

        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;

        visibleCards.slice(startIndex, endIndex).show();
        this.updatePaginationUI(totalItems, totalPages);
    }

    updatePaginationUI(totalItems, totalPages) {
        const $paginationList = $(this.paginationListSelector);

        if (!$paginationList.length) {
            return;
        }

        if (totalItems === 0) {
            $paginationList.html(
                '<li class="mobile-page-item"><button type="button" class="mobile-page-btn" disabled>No records</button></li>'
            );
            return;
        }

        const pageButtons = this.getPageButtons(totalPages);
        const isFirstPage = this.currentPage === 1;
        const isLastPage = this.currentPage >= totalPages;
        const items = [];

        items.push(this.renderPaginationButton('«', 1, isFirstPage, false, 'First page'));
        items.push(this.renderPaginationButton('‹', this.currentPage - 1, isFirstPage, false, 'Previous page'));

        pageButtons.forEach((page) => {
            if (page === 'ellipsis') {
                items.push('<li class="mobile-page-item"><button type="button" class="mobile-page-btn" disabled>…</button></li>');
                return;
            }

            items.push(this.renderPaginationButton(page, page, false, page === this.currentPage, 'Page ' + page));
        });

        items.push(this.renderPaginationButton('›', this.currentPage + 1, isLastPage, false, 'Next page'));
        items.push(this.renderPaginationButton('»', totalPages, isLastPage, false, 'Last page'));

        $paginationList.html(items.join(''));
    }

    getPageButtons(totalPages) {
        if (totalPages <= 5) {
            return Array.from({ length: totalPages }, (_, index) => index + 1);
        }

        if (this.currentPage <= 3) {
            return [1, 2, 3, 4, 'ellipsis', totalPages];
        }

        if (this.currentPage >= totalPages - 2) {
            return [1, 'ellipsis', totalPages - 3, totalPages - 2, totalPages - 1, totalPages];
        }

        return [1, 'ellipsis', this.currentPage - 1, this.currentPage, this.currentPage + 1, 'ellipsis', totalPages];
    }

    renderPaginationButton(label, page, disabled, active, ariaLabel) {
        return [
            '<li class="mobile-page-item">',
            '<button type="button" class="mobile-page-btn', active ? ' is-active' : '', '"',
            ' data-page="', page, '"',
            disabled ? ' disabled' : '',
            active ? ' aria-current="page"' : '',
            ' aria-label="', ariaLabel, '">',
            label,
            '</button>',
            '</li>'
        ].join('');
    }

    scrollToTop() {
        const container = $(this.cardSelector).first().parent();

        if (container.length) {
            $('html, body').animate({
                scrollTop: container.offset().top - 100
            }, 300);
        }
    }
}
