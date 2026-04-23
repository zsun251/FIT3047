/**
 * Users Index Page - DataTable and Mobile Functionality
 */
$(function(){
    // Initialize DataTable
    const table = $('#usersTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 10,
        columnDefs: [
            { orderable: false, targets: [6] }
        ],
        layout: {
            topStart: 'pageLength',
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        }
    });

    $('#usersTable').removeClass('dt-hidden');

    // Custom filter function using data attributes
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex, rowData, counter) {
        const activeFilter = $('.filter-btn.active').data('filter');

        if (activeFilter === 'all') {
            return true;
        }

        const row = table.row(dataIndex).node();
        const userType = $(row).data('user-type');

        return userType === activeFilter;
    });

    // Initialize mobile data table
    const mobileTable = new MobileDataTable({
        itemsPerPage: 10,
        defaultFilter: 'all',
        cardSelector: '.sub-card',
        filterAttribute: 'data-user-type',
        searchAttribute: 'data-search-text'
    });

    // Desktop & Mobile Filter button click handler
    $('.filter-btn').on('click', function() {
        const filter = $(this).data('filter');

        // Update active state
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');

        // Redraw desktop table with filter
        table.draw();

        // Update mobile filter
        mobileTable.setFilter(filter);
    });
});
