<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Patient $patient
 * @var \App\Model\Entity\VitalData[]|\Cake\Collection\CollectionInterface $vitalHistory
 */

use App\Policy\UserAccess;

$this->assign('title', $this->ContentBlock->text('health-records-patient-heading'));

// CSS
echo $this->Html->css('patient-health-records', ['block' => true]);

// Chart.js from CDN
echo $this->Html->script('https://cdn.jsdelivr.net/npm/chart.js', ['block' => true]);

// Custom JS
echo $this->Html->script('patient-health-records', ['block' => true]);

$vitalHistory = $vitalHistory ?? [];
$vitalHistoryArray = is_array($vitalHistory) ? $vitalHistory : $vitalHistory->toArray();
$dataCount = count($vitalHistoryArray);
// Reverse the array to show oldest to newest
$vitalHistoryArray = array_reverse($vitalHistoryArray);

// Function to calculate trend
function getTrend($array) {
    if (count($array) < 2) {
        return ['text' => 'Insufficient data', 'class' => 'neutral', 'icon' => 'dash-circle'];
    }
    $first = $array[0];
    $last = end($array);
    if ($last > $first) {
        return ['text' => 'Increasing', 'class' => 'increasing', 'icon' => 'arrow-up-circle-fill'];
    }
    if ($last < $first) {
        return ['text' => 'Decreasing', 'class' => 'decreasing', 'icon' => 'arrow-down-circle-fill'];
    }
    return ['text' => 'Stable', 'class' => 'stable', 'icon' => 'arrow-right-circle-fill'];
}

// Prepare chart data
if ($dataCount > 0) {
    $labels = [];
    $oxygen = [];
    $weight = [];
    $resp = [];
    $pulse = [];

    foreach ($vitalHistoryArray as $vital) {
        $labels[] = $vital->recorded_at ? $vital->recorded_at->format('M j, Y') : '';
        $oxygen[] = $vital->oxygen_saturation;
        $weight[] = $vital->weight;
        $resp[] = $vital->respiratory_rate;
        $pulse[] = $vital->pulse_oximetry;
    }
}


$identity = $this->request->getAttribute('identity');
$isStaff = UserAccess::isStaff($identity);

// Build breadcrumbs based on user type
$breadcrumbs = [];

if ($isStaff) {
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
        ['title' => 'Health Records', 'url' => ['controller' => 'VitalData', 'action' => 'index']],
        ['title' => 'Patient Health Records', 'url' => null]
    ];
} else {
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'patient']],
        ['title' => 'My Health Records', 'url' => null]
    ];
}
?>

<!-- Header Section -->
<?= $this->element('page_header', [
    'breadcrumbs' => $breadcrumbs
]) ?>

<div class="health-records-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1><i class="bi bi-heart-pulse"></i> <?= $this->ContentBlock->text('health-records-patient-heading') ?></h1>
            <p class="subtitle"><?= $this->ContentBlock->text('health-records-patient-intro-text') ?></p>
        </div>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i class="bi bi-plus-circle"></i> Add Health Data',
                ['controller' => 'VitalData', 'action' => 'add'],
                ['class' => 'btn btn-primary', 'escape' => false]
            ) ?>
        </div>
    </div>

    <!-- Patient Info Card -->
    <div class="info-card">
        <div class="info-icon">
            <i class="bi bi-person-badge"></i>
        </div>
        <div class="info-content">
            <h3>Patient ID</h3>
            <p><?= h($patient->id ?? 'Unknown') ?></p>
        </div>
        <div class="info-stats">
            <div class="stat-item">
                <span class="stat-label">Total Records</span>
                <span class="stat-value"><?= $dataCount ?></span>
            </div>
        </div>
    </div>

    <?php if ($dataCount === 0): ?>
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-icon">
                <i class="bi bi-clipboard2-pulse"></i>
            </div>
            <h2><?= $this->ContentBlock->text('health-records-empty-heading') ?></h2>
            <p><?= $this->ContentBlock->text('health-records-empty-text') ?></p>
            <?= $this->Html->link(
                '<i class="bi bi-plus-circle"></i> Add Your First Entry',
                ['controller' => 'VitalData', 'action' => 'add'],
                ['class' => 'btn btn-primary btn-lg', 'escape' => false]
            ) ?>
        </div>
    <?php else: ?>

        <!-- Trend Summary Cards -->
        <div class="trends-section">
            <h2 class="section-title"><i class="bi bi-graph-up"></i> Health Trends</h2>

            <div class="trend-cards">
                <?php
                $trends = [
                    ['label' => 'Oxygen Saturation', 'data' => $oxygen, 'icon' => 'heart-pulse', 'unit' => '%'],
                    ['label' => 'Respiratory Rate', 'data' => $resp, 'icon' => 'activity', 'unit' => ' bpm'],
                    ['label' => 'Weight', 'data' => $weight, 'icon' => 'speedometer', 'unit' => ' kg'],
                    ['label' => 'Pulse Oximetry', 'data' => $pulse, 'icon' => 'stopwatch', 'unit' => ''],
                    ['label' => 'Blood Pressure', 'data' => [], 'icon' => 'droplet', 'unit' => '', 'text_only' => true],
                ];

                foreach ($trends as $trend):
                    if (!empty($trend['text_only'])) {
                        // Blood pressure is text, not numeric
                        continue;
                    }
                    $trendInfo = getTrend($trend['data']);
                    $latestValue = end($trend['data']);
                    ?>
                    <div class="trend-card">
                        <div class="trend-icon">
                            <i class="bi bi-<?= h($trend['icon']) ?>"></i>
                        </div>
                        <div class="trend-info">
                            <h4><?= h($trend['label']) ?></h4>
                            <?php if ($latestValue !== null && $latestValue !== ''): ?>
                                <p class="trend-value"><?= h($latestValue) ?><?= h($trend['unit']) ?></p>
                            <?php else: ?>
                                <p class="trend-value text-muted">—</p>
                            <?php endif; ?>
                            <div class="trend-status trend-<?= h($trendInfo['class']) ?>">
                                <i class="bi bi-<?= h($trendInfo['icon']) ?>"></i>
                                <?= h($trendInfo['text']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($dataCount >= 2): ?>
            <!-- Chart Section -->
            <div class="chart-section">
                <h2 class="section-title"><i class="bi bi-graph-up-arrow"></i> Vitals Over Time</h2>

                <div class="chart-container">
                    <canvas id="vitalsChart"></canvas>
                </div>

                <script>
                    // Pass data to JavaScript
                    const vitalsChartData = {
                        labels: <?= json_encode($labels) ?>,
                        oxygen: <?= json_encode($oxygen) ?>,
                        resp: <?= json_encode($resp) ?>,
                        weight: <?= json_encode($weight) ?>,
                        pulse: <?= json_encode($pulse) ?>
                    };
                </script>
            </div>
        <?php else: ?>
            <div class="chart-notice">
                <i class="bi bi-info-circle"></i>
                <p><?= $this->ContentBlock->text('health-records-insufficient-chart-data-text') ?></p>
            </div>
        <?php endif; ?>

        <!-- Data Table -->
        <div class="table-section">
            <h2 class="section-title"><i class="bi bi-table"></i> Detailed Records</h2>

            <div class="table-responsive">
                <table class="vitals-table">
                    <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th><i class="bi bi-droplet"></i> Blood Pressure</th>
                        <th><i class="bi bi-heart-pulse"></i> O₂ Saturation</th>
                        <th><i class="bi bi-activity"></i> Respiratory Rate</th>
                        <th><i class="bi bi-stopwatch"></i> Pulse Oximetry</th>
                        <th><i class="bi bi-speedometer"></i> Weight</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($vitalHistoryArray as $vital): ?>
                        <tr>
                            <td class="date-cell">
                                <?php if ($vital->recorded_at): ?>
                                    <strong><?= h($vital->recorded_at->format('M d, Y')) ?></strong>
                                    <span class="time"><?= h($vital->recorded_at->format('g:i A')) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $vital->blood_pressure ? h($vital->blood_pressure) : '<span class="text-muted">—</span>' ?></td>
                            <td><?= $vital->oxygen_saturation ? h($vital->oxygen_saturation) . '%' : '<span class="text-muted">—</span>' ?></td>
                            <td><?= $vital->respiratory_rate ? h($vital->respiratory_rate) . ' bpm' : '<span class="text-muted">—</span>' ?></td>
                            <td><?= $vital->pulse_oximetry ? h($vital->pulse_oximetry) : '<span class="text-muted">—</span>' ?></td>
                            <td><?= $vital->weight ? h($vital->weight) . ' kg' : '<span class="text-muted">—</span>' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Footer Actions -->
    <div class="footer-actions">
        <?= $this->Html->link(
            '<i class="bi bi-arrow-left"></i> Back to Dashboard',
            ['controller' => 'Dashboard', 'action' => 'patient'],
            ['class' => 'btn btn-secondary', 'escape' => false]
        ) ?>
        <?= $this->Html->link(
            '<i class="bi bi-plus-circle"></i> Add Health Data',
            ['controller' => 'VitalData', 'action' => 'add'],
            ['class' => 'btn btn-primary', 'escape' => false]
        ) ?>
    </div>
</div>
