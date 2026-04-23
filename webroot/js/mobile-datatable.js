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

        this.init();
    }

    init() {
        this.setupSearch();
        this.setupPagination();
        this.applyFilters();
    }

    setupSearch() {
        const self = this;
        $('#mobileSearch').on('keyup', function() {
            self.searchQuery = $(this).val().toLowerCase();
            self.currentPage = 1;
            self.applyFilters();
        });
    }

    setupPagination() {
        const self = this;

        $('#mobilePrevBtn').on('click', function() {
            if (self.currentPage > 1) {
                self.currentPage--;
                self.applyFilters();
                self.scrollToTop();
            }
        });

        $('#mobileNextBtn').on('click', function() {
            self.currentPage++;
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

        // Apply status/type filter
        if (this.activeFilter !== 'all') {
            visibleCards = visibleCards.filter('[' + this.filterAttribute + '="' + this.activeFilter + '"]');
        }

        // Apply search filter
        if (this.searchQuery !== '') {
            const self = this;
            visibleCards = visibleCards.filter(function() {
                const searchText = $(this).attr(self.searchAttribute) || '';
                return searchText.indexOf(self.searchQuery) !== -1;
            });
        }

        // Hide all cards first
        $cards.hide();

        // Calculate pagination
        const totalItems = visibleCards.length;
        const totalPages = Math.ceil(totalItems / this.itemsPerPage);
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;

        // Show only the cards for current page
        visibleCards.slice(startIndex, endIndex).show();

        // Update pagination info
        this.updatePaginationUI(totalItems, totalPages);
    }

    updatePaginationUI(totalItems, totalPages) {
        if (totalItems > 0) {
            $('#mobilePageInfo').text('Page ' + this.currentPage + ' of ' + totalPages + ' (' + totalItems + ' items)');
        } else {
            $('#mobilePageInfo').text('No items found');
        }

        // Enable/disable pagination buttons
        $('#mobilePrevBtn').prop('disabled', this.currentPage === 1);
        $('#mobileNextBtn').prop('disabled', this.currentPage >= totalPages || totalItems === 0);
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
