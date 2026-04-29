/**
 * Patients Index Page - DataTable and Mobile Functionality
 */
$(function(){
    // Initialize DataTable
    const table = $('#patientsTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 10,
        columnDefs: [
            { orderable: false, targets: [5] }
        ],
        layout: {
            topStart: 'pageLength',
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        }
    });
    $('#patientsTable').removeClass('dt-hidden');

    // Initialize mobile data table
    const mobileTable = new MobileDataTable({
        itemsPerPage: 10,
        defaultFilter: 'all',
        cardSelector: '.sub-card',
        searchAttribute: 'data-search-text'
    });
});
