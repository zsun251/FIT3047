<?php
/**
 * src/templates/element/data_table.php
 *
 * Reusable table shell for every admin page that shows a DataTable.
 * Renders the outer card, desktop table, and mobile card list.
 * The calling template provides all the inner content via variables.
 *
 * Required variables:
 *   string $tableId          – unique HTML id, e.g. 'recentTable'
 *   string $tableTitle       – card header title, e.g. 'Recent Submissions'
 *   string $thead            – full <tr>…</tr> markup for the thead
 *   string $tbody            – full <tr>…</tr> row markup for the tbody
 *   string $mobileCards      – full .sub-card markup for the mobile list
 *
 * Optional variables:
 *   array  $viewAllUrl       – CakePHP URL array for the "View all →" link
 *   string $searchPlaceholder – overrides default search box placeholder
 *   string $emptyMessage     – overrides default "No results found." text
 *   string $filters          – optional HTML markup for header filters
 *   string $headerActions    – Set action links on the header
 */
$searchPlaceholder = $searchPlaceholder ?? 'Search…';
$emptyMessage      = $emptyMessage      ?? 'No results found.';
$viewAllUrl        = $viewAllUrl        ?? null;
$filters           = $filters           ?? null;
$headerActions     = $headerActions     ?? null;
$mobileSearchId    = $tableId . 'Search';
$mobileContainerId = $tableId . 'Cards';
$mobileEmptyId     = $tableId . 'Empty';
$mobileInfoId      = $tableId . 'Info';
$mobilePagId       = $tableId . 'Pagination';
?>
<div class="table-card mb-5">
    <!-- Card header -->
    <div class="table-card-header">
        <div class="d-flex align-items-center gap-3 flex-wrap w-100">
            <span class="table-card-title"><?= h($tableTitle) ?></span>

            <?php if ($filters): ?>
                <div class="table-filters">
                    <?= $filters ?>
                </div>
            <?php endif; ?>

            <div class="ms-auto d-flex gap-2">
                <?php if ($viewAllUrl): ?>
                    <?= $this->Html->link('View all →', $viewAllUrl, ['class' => 'view-all-link']) ?>
                <?php endif; ?>
                <?php if ($headerActions): ?>
                    <?= $headerActions ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ══ DESKTOP: DataTables ══════════════════════════════════ -->
    <div class="desktop-table-wrap">
        <table id="<?= h($tableId) ?>" class="table table-hover mb-0">
            <thead><?= $thead ?></thead>
            <tbody><?= $tbody ?></tbody>
        </table>
    </div>

    <!-- ══ MOBILE: Card list ════════════════════════════════════ -->
    <div class="mobile-cards-wrap">
<!--        --><?php //if ($filters): ?>
<!--            <div class="table-filters mb-3">-->
<!--                --><?php //= $filters ?>
<!--            </div>-->
<!--        --><?php //endif; ?>

        <div class="cards-search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input class="cards-search"
                   type="search"
                   id="<?= h($mobileSearchId) ?>"
                   placeholder="<?= h($searchPlaceholder) ?>">
        </div>

        <div id="<?= h($mobileContainerId) ?>">
            <?= $mobileCards ?>
        </div>

        <div class="cards-empty" id="<?= h($mobileEmptyId) ?>" style="display:none">
            <?= h($emptyMessage) ?>
        </div>

        <div class="cards-info"       id="<?= h($mobileInfoId) ?>"></div>
        <div class="cards-pagination" id="<?= h($mobilePagId) ?>"></div>
    </div>
</div>
