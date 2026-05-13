<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\VitalData> $vitalData
 * @var \Cake\Collection\CollectionInterface|array $patients
 * @var array $filters
 * @var bool $hasFilters
 */

use App\Policy\UserAccess;

$this->assign('title', 'Vital Data Management');

$this->start('datatables_css');
echo $this->Html->css([
    'https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css',
    'datatable-shared',
    'vital-data-index'
]);
$this->end();

$this->start('datatables_js');
echo $this->Html->script([
    'https://cdn.datatables.net/2.1.8/js/dataTables.min.js',
    'https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js',
]);
$this->end();

$this->Html->script('mobile-datatable', ['block' => 'script']);
$this->Html->script('vital-data-index', ['block' => 'script']);

$identity = $this->request->getAttribute('identity');
$isStaff = UserAccess::isStaff($identity);

$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
    ['title' => 'Health Records', 'url' => null]
];
?>

<!-- Header Section -->
<div class="dash-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <?= $this->element('page_header', [
            'breadcrumbs' => $breadcrumbs,
            'title' => 'Patient Health Records',
            'subtitle' => 'Monitor patient health metrics'
        ]) ?>
    </div>
    <div>
        <?= $this->Html->link(
            '<i class="bi bi-plus-circle"></i> New Record',
            ['action' => 'add'],
            [
                'class' => 'btn btn-primary',
                'escape' => false
            ]
        ) ?>
    </div>
</div>
<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card-total">
            <div class="stat-label">Total Records</div>
            <div class="stat-value"><?= count($vitalData) ?></div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card stat-card-symptoms">
            <div class="stat-label">With Symptoms</div>
            <div class="stat-value">
                <?= collection($vitalData)->countBy(fn($v) => !empty($v->symptoms))->toArray()[true] ?? 0 ?>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card stat-card-today">
            <div class="stat-label">Today</div>
            <div class="stat-value">
                <?= collection($vitalData)->countBy(fn($v) => $v->recorded_at->isToday())->toArray()[true] ?? 0 ?>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card stat-card-week">
            <div class="stat-label">This Week</div>
            <div class="stat-value">
                <?php
                $weekStart = (new \Cake\I18n\DateTime())->startOfWeek();
                $weekEnd = (new \Cake\I18n\DateTime())->endOfWeek();
                $thisWeekCount = collection($vitalData)->countBy(function ($v) use ($weekStart, $weekEnd) {
                    return $v->recorded_at >= $weekStart && $v->recorded_at <= $weekEnd;
                })->toArray()[true] ?? 0;
                echo $thisWeekCount;
                ?>
            </div>
        </div>
    </div>
</div>

<div class="vital-list-card">
    <div class="vital-list-toolbar">
        <?= $this->Form->create(null, [
            'type' => 'get',
            'class' => 'filter-toolbar-form',
            'valueSources' => ['query'],
            'id' => 'vitalFiltersForm'
        ]) ?>

        <div class="filter-toolbar-grid">
            <div class="filter-field filter-field-patient">
                <?= $this->Form->control('patient_id', [
                    'type' => 'select',
                    'options' => $patients,
                    'empty' => 'All patients',
                    'label' => 'Patient',
                    'value' => $filters['patient_id'] ?? '',
                    'class' => 'form-select'
                ]) ?>
            </div>

            <div class="filter-field filter-field-symptoms">
                <label class="form-label" for="symptomFilterInput">Symptoms</label>

                <input
                    type="hidden"
                    name="symptom_filter"
                    id="symptomFilterInput"
                    value="<?= h($filters['symptom_filter'] ?? 'any') ?>"
                >

                <div class="symptom-chip-group" id="symptomChipGroup">
                    <?php
                    $symptomOptions = [
                        'with' => 'With symptoms',
                        'none' => 'No symptoms',
                    ];
                    ?>

                    <?php foreach ($symptomOptions as $value => $label): ?>
                        <button
                            type="button"
                            class="symptom-chip <?= ($filters['symptom_filter'] ?? 'any') === $value ? 'is-active' : '' ?>"
                            data-symptom-filter="<?= h($value) ?>"
                            aria-pressed="<?= (($filters['symptom_filter'] ?? 'any') === $value) ? 'true' : 'false' ?>"
                        >
                            <?= h($label) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="filter-field filter-field-date">
                <label class="form-label" for="datePresetInput">Date</label>

                <input
                    type="hidden"
                    name="date_preset"
                    id="datePresetInput"
                    value="<?= h($filters['date_preset'] ?? 'any') ?>"
                >

                <div class="date-chip-group" id="datePresetChipGroup">
                    <?php
                    $datePresetOptions = [
                        'any' => 'Any time',
                        'today' => 'Today',
                        'last_7_days' => 'Last 7 days',
                        'last_30_days' => 'Last 30 days',
                        'custom' => 'Custom',
                    ];
                    ?>

                    <?php foreach ($datePresetOptions as $value => $label): ?>
                        <button
                            type="button"
                            class="date-chip <?= ($filters['date_preset'] ?? 'any') === $value ? 'is-active' : '' ?>"
                            data-date-preset="<?= h($value) ?>"
                            aria-pressed="<?= (($filters['date_preset'] ?? 'any') === $value) ? 'true' : 'false' ?>"
                        >
                            <?= h($label) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="filter-actions">
                <?= $this->Form->button('<i class="bi bi-funnel"></i> Apply', [
                    'type' => 'submit',
                    'class' => 'btn btn-primary',
                    'escapeTitle' => false
                ]) ?>

                <?= $this->Html->link(
                    '<i class="bi bi-x-circle"></i> Clear',
                    ['action' => 'index'],
                    ['class' => 'btn btn-outline-secondary', 'escape' => false]
                ) ?>
            </div>
        </div>

        <div
            class="symptom-severity-row <?= (($filters['symptom_filter'] ?? 'any') === 'with') ? 'is-visible' : '' ?>"
            id="severityRow"
        >
            <div class="filter-field">
                <?= $this->Form->control('severity', [
                    'type' => 'select',
                    'options' => [
                        'mild' => 'Mild',
                        'moderate' => 'Moderate',
                        'severe' => 'Severe',
                    ],
                    'empty' => 'Any severity',
                    'label' => 'Severity',
                    'value' => $filters['severity'] ?? '',
                    'class' => 'form-select',
                    'id' => 'severityField',
                    'disabled' => (($filters['symptom_filter'] ?? 'any') !== 'with')
                ]) ?>
            </div>
        </div>

        <div
            class="custom-date-row <?= ($filters['date_preset'] ?? '') === 'custom' ? 'is-visible' : '' ?>"
            id="customDateRow"
        >
            <div class="filter-field" id="dateFromGroup">
                <label for="dateFromField" class="form-label">Start date</label>
                <input
                    type="date"
                    name="date_from"
                    id="dateFromField"
                    class="form-control"
                    value="<?= h($filters['date_from'] ?? '') ?>"
                >
            </div>

            <div class="filter-field" id="dateToGroup">
                <label for="dateToField" class="form-label">End date</label>
                <input
                    type="date"
                    name="date_to"
                    id="dateToField"
                    class="form-control"
                    value="<?= h($filters['date_to'] ?? '') ?>"
                >
            </div>

            <div class="custom-date-help">
                <div class="custom-date-help-title">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>How custom date filtering works</span>
                </div>

                <div class="custom-date-help-items">
                    <span class="custom-date-help-pill help-pill-end">
                        Only <strong>end date</strong> → shows everything up to that date
                    </span>
                    <span class="custom-date-help-pill help-pill-start">
                        Only <strong>start date</strong> → shows everything from that date onward
                    </span>
                    <span class="custom-date-help-pill help-pill-range">
                        <strong>Both dates</strong> → filters to the selected date range
                    </span>
                </div>
            </div>
        </div>

        <?= $this->Form->end() ?>
    </div>

    <div class="records-card-body">
        <div class="d-md-none mobile-search-wrap">
            <input type="text" id="mobileSearch" class="form-control" placeholder="Search vital data...">
        </div>

        <div class="table-responsive d-none d-md-block">
            <table id="vitalDataTable" class="table table-striped table-hover dt-hidden mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Vitals</th>
                    <th>Symptoms</th>
                    <th>Recorded</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($vitalData as $vitalDataEntity): ?>
                    <?php
                    $patientName = '';
                    if ($vitalDataEntity->hasValue('patient')) {
                        $patientName = trim(
                            ($vitalDataEntity->patient->user->first_name ?? '') . ' ' .
                            ($vitalDataEntity->patient->user->last_name ?? '')
                        );

                        if ($patientName === '') {
                            $patientName = 'Patient #' . $vitalDataEntity->patient->id;
                        }
                    }
                    ?>

                    <tr>
                        <td class="align-middle">
                            <?= $this->Html->link(
                                '<span class="badge bg-secondary">#' . $this->Number->format($vitalDataEntity->id) . '</span>',
                                ['action' => 'view', $vitalDataEntity->id],
                                ['escape' => false, 'class' => 'text-decoration-none']
                            ) ?>
                        </td>

                        <td class="align-middle">
                            <?php if ($vitalDataEntity->hasValue('patient')): ?>
                                <?= $this->Html->link(
                                    h($patientName),
                                    ['controller' => 'Patients', 'action' => 'view', $vitalDataEntity->patient->id],
                                    ['class' => 'fw-bold text-decoration-none']
                                ) ?>
                            <?php endif; ?>
                        </td>

                        <td class="align-middle">
                            <div class="vitals-summary">
                                <?php if ($vitalDataEntity->oxygen_saturation !== null): ?>
                                    <div class="vital-item">
                                        <i class="bi bi-heart-pulse text-danger"></i>
                                        <strong>O₂:</strong> <?= $this->Number->format($vitalDataEntity->oxygen_saturation) ?>%
                                    </div>
                                <?php endif; ?>

                                <?php if ($vitalDataEntity->respiratory_rate !== null): ?>
                                    <div class="vital-item">
                                        <i class="bi bi-activity text-primary"></i>
                                        <strong>RR:</strong> <?= $this->Number->format($vitalDataEntity->respiratory_rate) ?> bpm
                                    </div>
                                <?php endif; ?>

                                <?php if ($vitalDataEntity->blood_pressure !== null): ?>
                                    <div class="vital-item">
                                        <i class="bi bi-droplet text-info"></i>
                                        <strong>BP:</strong> <?= h($vitalDataEntity->blood_pressure) ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($vitalDataEntity->weight !== null): ?>
                                    <div class="vital-item">
                                        <i class="bi bi-speedometer text-success"></i>
                                        <strong>Weight:</strong> <?= $this->Number->format($vitalDataEntity->weight) ?> kg
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>

                        <td class="align-middle">
                            <?php if (!empty($vitalDataEntity->symptoms)): ?>
                                <div class="symptoms-container">
                                    <?php foreach (array_slice($vitalDataEntity->symptoms, 0, 3) as $symptom): ?>
                                        <span class="symptom-card severity-<?= h(strtolower($symptom->_joinData->severity ?? 'mild')) ?>">
                                            <?= h($symptom->symptom_name) ?>
                                        </span>
                                    <?php endforeach; ?>

                                    <?php if (count($vitalDataEntity->symptoms) > 3): ?>
                                        <span class="symptom-card more-badge">
                                            +<?= count($vitalDataEntity->symptoms) - 3 ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="no-symptoms">No symptoms</span>
                            <?php endif; ?>
                        </td>

                        <td class="align-middle nowrap">
                            <div><?= h($vitalDataEntity->recorded_at->format('M d, Y')) ?></div>
                            <small class="text-muted"><?= h($vitalDataEntity->recorded_at->format('g:i A')) ?></small>
                        </td>

                        <td class="align-middle text-center nowrap">
                            <div class="btn-group" role="group">
                                <?= $this->Html->link(
                                    '<i class="bi bi-clock-history"></i>',
                                    ['action' => 'patientHistory', $vitalDataEntity->patient_id],
                                    [
                                        'class' => 'btn btn-sm btn-outline-info',
                                        'escape' => false,
                                        'title' => 'View Patient History'
                                    ]
                                ) ?>

                                <?= $this->Html->link(
                                    '<i class="bi bi-eye"></i>',
                                    ['action' => 'view', $vitalDataEntity->id],
                                    [
                                        'class' => 'btn btn-sm btn-outline-primary',
                                        'escape' => false,
                                        'title' => 'View'
                                    ]
                                ) ?>

                                <?= $this->Html->link(
                                    '<i class="bi bi-pencil"></i>',
                                    ['action' => 'edit', $vitalDataEntity->id],
                                    [
                                        'class' => 'btn btn-sm btn-outline-secondary',
                                        'escape' => false,
                                        'title' => 'Edit'
                                    ]
                                ) ?>

                                <?= $this->Form->postLink(
                                    '<i class="bi bi-trash"></i>',
                                    ['action' => 'delete', $vitalDataEntity->id],
                                    [
                                        'class' => 'btn btn-sm btn-outline-danger',
                                        'escape' => false,
                                        'confirm' => __('Are you sure you want to delete record #{0}?', $vitalDataEntity->id),
                                        'title' => 'Delete'
                                    ]
                                ) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-md-none records-mobile-body" id="mobileCardsContainer">
            <?php foreach ($vitalData as $vitalDataEntity): ?>
                <?php
                $patientName = '';
                if ($vitalDataEntity->hasValue('patient')) {
                    $patientName = trim(
                        ($vitalDataEntity->patient->user->first_name ?? '') . ' ' .
                        ($vitalDataEntity->patient->user->last_name ?? '')
                    );

                    if ($patientName === '') {
                        $patientName = 'Patient #' . $vitalDataEntity->patient->id;
                    }
                }

                $mobileSearchParts = [
                    $vitalDataEntity->id,
                    $vitalDataEntity->patient->id ?? '',
                    $patientName,
                    $vitalDataEntity->recorded_at ? $vitalDataEntity->recorded_at->format('Y-m-d M d, Y') : '',
                ];

                foreach ($vitalDataEntity->symptoms ?? [] as $symptom) {
                    $mobileSearchParts[] = $symptom->symptom_name;
                    $mobileSearchParts[] = $symptom->_joinData->severity ?? '';
                }
                ?>

                <div class="sub-card" data-search-text="<?= h(strtolower(implode(' ', $mobileSearchParts))) ?>">
                    <div class="card-top">
                        <div>
                            <?= $this->Html->link(
                                'Health Record <span class="badge bg-secondary ms-2">#' . h($vitalDataEntity->id) . '</span>',
                                ['action' => 'view', $vitalDataEntity->id],
                                ['class' => 'card-name text-decoration-none', 'escape' => false]
                            ) ?>

                            <?php if ($vitalDataEntity->hasValue('patient')): ?>
                                <div class="card-phone text-muted">
                                    <?= $this->Html->link(
                                        h($patientName),
                                        ['controller' => 'Patients', 'action' => 'view', $vitalDataEntity->patient->id]
                                    ) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-field">
                        <strong><i class="bi bi-heart-pulse"></i> Vital Signs:</strong>
                        <div class="mt-2">
                            <?php if ($vitalDataEntity->oxygen_saturation !== null): ?>
                                <div>O₂ Saturation: <strong><?= $this->Number->format($vitalDataEntity->oxygen_saturation) ?>%</strong></div>
                            <?php endif; ?>

                            <?php if ($vitalDataEntity->respiratory_rate !== null): ?>
                                <div>Respiratory Rate: <strong><?= $this->Number->format($vitalDataEntity->respiratory_rate) ?> bpm</strong></div>
                            <?php endif; ?>

                            <?php if ($vitalDataEntity->blood_pressure !== null): ?>
                                <div>Blood Pressure: <strong><?= h($vitalDataEntity->blood_pressure) ?></strong></div>
                            <?php endif; ?>

                            <?php if ($vitalDataEntity->pulse_oximetry !== null): ?>
                                <div>Pulse Oximetry: <strong><?= $this->Number->format($vitalDataEntity->pulse_oximetry) ?></strong></div>
                            <?php endif; ?>

                            <?php if ($vitalDataEntity->weight !== null): ?>
                                <div>Weight: <strong><?= $this->Number->format($vitalDataEntity->weight) ?> kg</strong></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($vitalDataEntity->symptoms)): ?>
                        <div class="card-field">
                            <strong><i class="bi bi-clipboard2-pulse"></i> Symptoms:</strong>
                            <div class="symptoms-list mt-2">
                                <?php foreach ($vitalDataEntity->symptoms as $symptom): ?>
                                    <span class="symptom-badge severity-<?= h(strtolower($symptom->_joinData->severity ?? 'mild')) ?>">
                                        <?= h($symptom->symptom_name) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="card-date">
                        <i class="bi bi-clock-history"></i>
                        Recorded: <?= h($vitalDataEntity->recorded_at->format('M d, Y')) ?> at <?= h($vitalDataEntity->recorded_at->format('g:i A')) ?>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <?= $this->Html->link(
                            '<i class="bi bi-clock-history"></i> History',
                            ['action' => 'patientHistory', $vitalDataEntity->patient_id],
                            ['class' => 'btn btn-sm btn-outline-info flex-fill', 'escape' => false]
                        ) ?>

                        <?= $this->Html->link(
                            '<i class="bi bi-eye"></i> View',
                            ['action' => 'view', $vitalDataEntity->id],
                            ['class' => 'btn btn-sm btn-outline-primary flex-fill', 'escape' => false]
                        ) ?>

                        <?= $this->Html->link(
                            '<i class="bi bi-pencil"></i> Edit',
                            ['action' => 'edit', $vitalDataEntity->id],
                            ['class' => 'btn btn-sm btn-outline-secondary flex-fill', 'escape' => false]
                        ) ?>

                        <?= $this->Form->postLink(
                            '<i class="bi bi-trash"></i> Delete',
                            ['action' => 'delete', $vitalDataEntity->id],
                            [
                                'class' => 'btn btn-sm btn-outline-danger flex-fill',
                                'escape' => false,
                                'confirm' => __('Delete record #{0}?', $vitalDataEntity->id)
                            ]
                        ) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="d-md-none mt-3 mobile-pagination-wrap" id="mobilePagination">
            <div class="d-flex justify-content-between align-items-center">
                <button id="mobilePrevBtn" class="btn btn-sm btn-outline-secondary" disabled>
                    <i class="bi bi-chevron-left"></i> Previous
                </button>
                <span id="mobilePageInfo" class="text-muted small"></span>
                <button id="mobileNextBtn" class="btn btn-sm btn-outline-secondary">
                    Next <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>
