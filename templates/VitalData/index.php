<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\VitalData> $vitalData
 */
$this->assign('title', 'Vital Data Management');
// CSS
$this->start('datatables_css');
echo $this->Html->css([
    'https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css',
    'datatable-shared',
    'vital-data-index'
]);
$this->end();
// JS
$this->start('datatables_js');
echo $this->Html->script([
    'https://cdn.datatables.net/2.1.8/js/dataTables.min.js',
    'https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js',
]);
$this->end();
// Page-specific JS
$this->Html->script('mobile-datatable', ['block' => 'script']);
$this->Html->script('vital-data-index', ['block' => 'script']);
?>
<!-- Header Section -->
<div class="dash-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="dash-title mb-1">Patient Health Records</h1>
        <p class="text-muted mb-0">Monitor patient health metrics</p>
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
        <div class="stat-card">
            <div class="stat-label">Total Records</div>
            <div class="stat-value"><?= count($vitalData) ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">With Symptoms</div>
            <div class="stat-value stat-unread">
                <?= collection($vitalData)->countBy(fn($v) => !empty($v->symptoms))->toArray()[true] ?? 0 ?>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Today</div>
            <div class="stat-value stat-read">
                <?= collection($vitalData)->countBy(fn($v) => $v->recorded_at->isToday())->toArray()[true] ?? 0 ?>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">This Week</div>
            <div class="stat-value stat-arch">
                <?php
                $weekStart = (new \Cake\I18n\DateTime())->startOfWeek();
                $weekEnd = (new \Cake\I18n\DateTime())->endOfWeek();
                $thisWeekCount = collection($vitalData)->countBy(function($v) use ($weekStart, $weekEnd) {
                    return $v->recorded_at >= $weekStart && $v->recorded_at <= $weekEnd;
                })->toArray()[true] ?? 0;
                echo $thisWeekCount;
                ?>
            </div>
        </div>
    </div>
</div>
<!-- Mobile Search (visible only on mobile) -->
<div class="d-md-none mb-3">
    <input type="text" id="mobileSearch" class="form-control" placeholder="Search vital data...">
</div>
<!-- Desktop Table -->
<div class="table-responsive d-none d-md-block">
    <table id="vitalDataTable" class="table table-striped table-hover dt-hidden">
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
            <tr>
                <td class="align-middle">
                    <span class="badge bg-secondary">#<?= $this->Number->format($vitalDataEntity->id) ?></span>
                </td>
                <td class="align-middle">
                    <?php if ($vitalDataEntity->hasValue('patient')): ?>
                        <?= $this->Html->link(
                            'Patient #' . $vitalDataEntity->patient->id,
                            ['controller' => 'Patients', 'action' => 'view', $vitalDataEntity->patient->id],
                            ['class' => 'fw-bold text-decoration-none']
                        ) ?>
                    <?php else: ?>
                        <span class="text-muted">N/A</span>
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
                        <span class="text-muted small">No symptoms</span>
                    <?php endif; ?>
                </td>
                <td class="align-middle nowrap">
                    <div><?= h($vitalDataEntity->recorded_at->format('M d, Y')) ?></div>
                    <small class="text-muted"><?= h($vitalDataEntity->recorded_at->format('g:i A')) ?></small>
                </td>
                <td class="align-middle text-center nowrap">
                    <div class="btn-group" role="group">
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
<!-- Mobile Cards -->
<div class="d-md-none" id="mobileCardsContainer">
    <?php foreach ($vitalData as $vitalDataEntity): ?>
        <div class="sub-card"
             data-search-text="<?= h(strtolower($vitalDataEntity->id . ' ' . ($vitalDataEntity->patient->id ?? ''))) ?>">
            <div class="card-top">
                <div>
                    <div class="card-name">
                        Vital Record
                        <span class="badge bg-secondary ms-2">#<?= h($vitalDataEntity->id) ?></span>
                    </div>
                    <?php if ($vitalDataEntity->hasValue('patient')): ?>
                        <div class="card-phone text-muted">
                            <?= $this->Html->link(
                                'Patient #' . $vitalDataEntity->patient->id,
                                ['controller' => 'Patients', 'action' => 'view', $vitalDataEntity->patient->id]
                            ) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Vitals -->
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
            <!-- Symptoms -->
            <?php if (!empty($vitalDataEntity->symptoms)): ?>
                <div class="card-field">
                    <strong><i class="bi bi-clipboard2-pulse"></i> Symptoms:</strong>
                    <div class="symptoms-list mt-2">
                        <?php foreach ($vitalDataEntity->symptoms as $symptom): ?>
                            <span class="symptom-badge severity-<?= h(strtolower($symptom->_joinData->severity ?? 'mild')) ?>"><?= h($symptom->symptom_name) ?></span>
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
<!-- Mobile Pagination -->
<div class="d-md-none mt-3" id="mobilePagination">
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
