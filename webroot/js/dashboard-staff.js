/**
 * Dashboard Staff Page - Contact Messages DataTable
 */
$(function(){
    // Initialize DataTable
    const table = $('#recentTable').DataTable({
        order: [[4, 'desc']],
        pageLength: 10,
        columnDefs: [
            { orderable: false, targets: [2, 5] }
        ],
        layout: {
            topStart: 'pageLength',
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        }
    });

    $('#recentTable').removeClass('dt-hidden');

    // Initialize mobile data table
    const mobileTable = new MobileDataTable({
        itemsPerPage: 10,
        defaultFilter: 'all',
        cardSelector: '.sub-card',
        filterAttribute: 'data-status',
        searchAttribute: 'data-search-text'
    });

    // Filter button click handler (for mobile - desktop uses links)
    $('.filter-btn').on('click', function(e) {
        // Only handle if it's a button, not a link
        if ($(this).is('button')) {
            e.preventDefault();
            const filter = $(this).data('filter');

            // Update active state
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');

            // Update mobile filter
            mobileTable.setFilter(filter);
        }
    });
});
