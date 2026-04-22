<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Patient|null $patient
 * @var \App\Model\Entity\VitalData[]|\Cake\Collection\CollectionInterface|array $vitalHistory
 */

$this->assign('title', 'Health Records');

$vitalHistory = $vitalHistory ?? [];

// Convert to array (for counting and chart/table output)
$vitalHistoryArray = is_array($vitalHistory) ? $vitalHistory : $vitalHistory->toArray();

$dataCount = count($vitalHistoryArray);
?>

<h1>Health Records</h1>

<?php if ($patient): ?>
    <p>
        <strong>Patient ID:</strong>
        <?= h($patient->id) ?>
    </p>
<?php endif; ?>

<?php if ($dataCount === 0): ?>

    <p><strong>There are no historical records available.</strong></p>

    <table border="1" cellpadding="8">
        <thead>
        <tr>
            <th>Date</th>
            <th>Blood Pressure</th>
            <th>Oxygen Saturation</th>
            <th>Respiratory Rate</th>
            <th>Lung Function</th>
            <th>Pulse Oximetry</th>
            <th>Weight</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="7">No records found.</td>
        </tr>
        </tbody>
    </table>

<?php else: ?>

    <?php
    $labels = [];

    $oxygen = [];
    $weight = [];
    $lung = [];
    $bp = [];
    $resp = [];
    $pulse = [];

    foreach ($vitalHistoryArray as $vital) {
        $labels[] = $vital->recorded_at ? $vital->recorded_at->format('d M Y H:i') : '';

        $oxygen[] = $vital->oxygen_saturation;
        $weight[] = $vital->weight;
        $lung[] = $vital->lung_function;
        $bp[] = $vital->blood_pressure;
        $resp[] = $vital->respiratory_rate;
        $pulse[] = $vital->pulse_oximetry;
    }

    function getTrend($array)
    {
        if (count($array) < 2) {
            return 'Not enough data';
        }

        $first = $array[0];
        $last = end($array);

        if ($last > $first) {
            return 'Increasing ↑';
        }

        if ($last < $first) {
            return 'Decreasing ↓';
        }

        return 'Stable →';
    }
    ?>

    <h2>Vitals Trend Overview</h2>

    <?php if ($dataCount < 2): ?>

        <p><strong>Not enough data to generate chart.</strong></p>

    <?php else: ?>

        <canvas id="vitalsChart"></canvas>

        <script>
            new Chart(document.getElementById('vitalsChart'), {
                type: 'line',
                data: {
                    labels: <?= json_encode($labels) ?>,
                    datasets: [
                        { label: 'Oxygen Saturation', data: <?= json_encode($oxygen) ?> },
                        { label: 'Weight', data: <?= json_encode($weight) ?> },
                        { label: 'Lung Function', data: <?= json_encode($lung) ?> },
                        { label: 'Blood Pressure', data: <?= json_encode($bp) ?> },
                        { label: 'Respiratory Rate', data: <?= json_encode($resp) ?> },
                        { label: 'Pulse Oximetry', data: <?= json_encode($pulse) ?> }
                    ]
                }
            });
        </script>

    <?php endif; ?>

    <br>

    <h3>Trend Summary</h3>
    <ul>
        <li>Oxygen: <?= getTrend($oxygen) ?></li>
        <li>Weight: <?= getTrend($weight) ?></li>
        <li>Lung Function: <?= getTrend($lung) ?></li>
        <li>Blood Pressure: <?= getTrend($bp) ?></li>
        <li>Respiratory Rate: <?= getTrend($resp) ?></li>
        <li>Pulse Oximetry: <?= getTrend($pulse) ?></li>
    </ul>

    <br><br>

    <table border="1" cellpadding="8">
        <thead>
        <tr>
            <th>Date</th>
            <th>Blood Pressure</th>
            <th>Oxygen Saturation</th>
            <th>Respiratory Rate</th>
            <th>Lung Function</th>
            <th>Pulse Oximetry</th>
            <th>Weight</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($vitalHistoryArray as $vital): ?>
            <tr>
                <td><?= h($vital->recorded_at) ?></td>
                <td><?= h($vital->blood_pressure) ?></td>
                <td><?= h($vital->oxygen_saturation) ?></td>
                <td><?= h($vital->respiratory_rate) ?></td>
                <td><?= h($vital->lung_function) ?></td>
                <td><?= h($vital->pulse_oximetry) ?></td>
                <td><?= h($vital->weight) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>

<br>

<?= $this->Html->link(
    'Back to Dashboard',
    ['controller' => 'Dashboard', 'action' => 'patient']
) ?>

<?= $this->Html->link(
    'Input Health Data',
    ['controller' => 'VitalData', 'action' => 'add'],
    ['class' => 'button']
) ?>
