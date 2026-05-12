/**
 * Vital Data Index Page
 * - DataTable init
 * - Mobile card search/pagination
 * - Integrated filter toolbar interactions
 */
$(function () {
    if ($('#vitalDataTable').length) {
        $('#vitalDataTable').DataTable({
            order: [[4, 'desc']],
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

        $('#vitalDataTable').removeClass('dt-hidden');
    }

    if (typeof MobileDataTable === 'function' && $('#mobileCardsContainer').length) {
        new MobileDataTable({
            itemsPerPage: 10,
            defaultFilter: 'all',
            cardSelector: '.sub-card',
            searchAttribute: 'data-search-text'
        });
    }

    const $symptomFilterInput = $('#symptomFilterInput');
    const $symptomButtons = $('.symptom-chip[data-symptom-filter]');
    const $severityRow = $('#severityRow');
    const $severityField = $('#severityField');

    const $datePresetInput = $('#datePresetInput');
    const $datePresetButtons = $('.date-chip[data-date-preset]');
    const $customDateRow = $('#customDateRow');
    const $dateFromField = $('#dateFromField');
    const $dateToField = $('#dateToField');

    function updateSymptomFilterUI() {
        const symptomFilter = $symptomFilterInput.val() || 'any';
        const showSeverity = symptomFilter === 'with';

        $symptomButtons.each(function () {
            const isActive = $(this).data('symptomFilter') === symptomFilter;
            $(this)
                .toggleClass('is-active', isActive)
                .attr('aria-pressed', isActive ? 'true' : 'false');
        });

        $severityRow.toggleClass('is-visible', showSeverity);

        if ($severityField.length) {
            $severityField.prop('disabled', !showSeverity);
        }
    }

    function updateDateFilterUI() {
        const preset = $datePresetInput.val() || 'any';
        const isCustom = preset === 'custom';

        $datePresetButtons.each(function () {
            const isActive = $(this).data('datePreset') === preset;
            $(this)
                .toggleClass('is-active', isActive)
                .attr('aria-pressed', isActive ? 'true' : 'false');
        });

        $customDateRow.toggleClass('is-visible', isCustom);

        if (!isCustom) {
            $dateFromField.prop('disabled', true);
            $dateToField.prop('disabled', true);
            return;
        }

        $dateFromField.prop('disabled', false);
        $dateToField.prop('disabled', false);
    }

    $symptomButtons.on('click', function () {
        const selectedFilter = $(this).data('symptomFilter');
        const currentFilter = $symptomFilterInput.val() || 'any';
        const nextFilter = currentFilter === selectedFilter ? 'any' : selectedFilter;

        $symptomFilterInput.val(nextFilter);

        if (nextFilter !== 'with' && $severityField.length) {
            $severityField.val('');
        }

        updateSymptomFilterUI();
    });

    $datePresetButtons.on('click', function () {
        const preset = $(this).data('datePreset');
        $datePresetInput.val(preset);
        updateDateFilterUI();
    });

    updateSymptomFilterUI();
    updateDateFilterUI();
});
