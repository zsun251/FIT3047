<h1>Patient History</h1>

<h3>Patient ID: <?= h($patient->id) ?></h3>

<table>
    <thead>
    <tr>
        <th>Date</th>
        <th>Blood Pressure</th>
        <th>Oxygen</th>
        <th>Lung Function</th>
        <th>Weight</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($vitalHistory as $vital): ?>
        <tr>
            <td><?= h($vital->recorded_at) ?></td>
            <td><?= h($vital->blood_pressure) ?></td>
            <td><?= h($vital->oxygen_saturation) ?></td>
            <td><?= h($vital->lung_function) ?></td>
            <td><?= h($vital->weight) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?= $this->Html->link('Back to Vital Data', ['controller' => 'VitalData', 'action' => 'index']) ?>
