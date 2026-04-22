$(function () {
    const table = $('#appointmentsTable').DataTable({
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

    $('#appointmentsTable').removeClass('dt-hidden');

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        if (settings.nTable.id !== 'appointmentsTable') {
            return true;
        }

        const activeFilter = $('.filter-btn.active').data('filter');

        if (activeFilter === 'all') {
            return true;
        }

        const row = table.row(dataIndex).node();
        const status = $(row).data('appointment-status');

        return status === activeFilter;
    });

    const mobileTable = new MobileDataTable({
        itemsPerPage: 10,
        defaultFilter: 'all',
        cardSelector: '.sub-card',
        filterAttribute: 'data-appointment-status',
        searchAttribute: 'data-search-text'
    });

    $('.filter-btn').on('click', function () {
        const filter = $(this).data('filter');

        $('.filter-btn').removeClass('active');
        $(this).addClass('active');

        table.draw();
        mobileTable.setFilter(filter);
    });
});
