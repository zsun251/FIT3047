/**
 * Vital Data Index Page - DataTable and Mobile Functionality
 */
$(function(){
    // Initialize DataTable
    const table = $('#vitalDataTable').DataTable({
        order: [[4, 'desc']], // Sort by Recorded date (column index 4)
        pageLength: 10,
        columnDefs: [
            { orderable: false, targets: [5] } // Actions column
        ],
        layout: {
            topStart: 'pageLength',
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        }
    });

    // Show table after initialization
    $('#vitalDataTable').removeClass('dt-hidden');

    // Initialize mobile data table
    const mobileTable = new MobileDataTable({
        itemsPerPage: 10,
        defaultFilter: 'all',
        cardSelector: '.sub-card',
        searchAttribute: 'data-search-text'
    });
});
