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
            topStart: null,
            topEnd: null,
            bottomStart: 'info',
            bottomEnd: 'paging'
        }
    });

    $('#recentTable').removeClass('dt-hidden');

    $('#tableSearch').on('keyup input', function () {
        table.search(this.value).draw();
    });

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


// Enables click functionality on table row.
document.addEventListener('DOMContentLoaded', function() {
    // Make table rows clickable
    const tableRows = document.querySelectorAll('#recentTable tbody tr');

    tableRows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on action buttons
            if (e.target.closest('td:last-child')) {
                return;
            }

            // Get the view link from the row
            const viewLink = this.querySelector('a[title="View"]');
            if (viewLink) {
                window.location.href = viewLink.href;
            }
        });
    });
});
