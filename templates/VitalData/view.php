<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\VitalData $vitalDataEntity
 */
?>
<div class="back-link">
    <?= $this->Html->link(
        '<i class="bi bi-arrow-left"></i><span>Back to Health Records</span>',
        ['controller' => 'VitalData', 'action' => 'index'],
        ['escape' => false]
    ) ?>
</div>
<div class="container-fluid px-0">
    <div class="vitalData view content">
        <?php
        $patientName = 'Patient ' . ($vitalDataEntity->patient->id ?? '');
        $words = explode(' ', $patientName);
        $initials = strtoupper($words[0][0] . ($words[1][0] ?? ''));
        ?>

        <div class="view-header d-flex align-items-center ps-2 pe-4 py-4">

            <div class="d-flex align-items-center gap-3">
                <?php
                $patientName = 'Patient ' . ($vitalDataEntity->patient->id ?? '');
                $initials = strtoupper(substr($patientName, 0, 1) . ($vitalDataEntity->patient->id ?? ''));
                ?>

                <div class="profile-avatar">
                    <?= $initials ?>
                </div>

                <div>
                    <h1 class="fw-bold mb-1">
                        <?= $patientName ?>
                    </h1>

                    <div class="text-muted">
                        Patient ID: <?= $vitalDataEntity->patient->id ?? 'N/A' ?>
                        &nbsp;&nbsp;•&nbsp;&nbsp;
                        Recorded: <?= h($vitalDataEntity->recorded_at) ?>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 ms-auto">

                <?= $this->Html->link(
                    '<i class="bi bi-pencil"></i> Edit',
                    ['action' => 'edit', $vitalDataEntity->id],
                    ['class' => 'btn btn-edit px-3 py-2', 'escape' => false]
                ) ?>

                <?= $this->Form->postLink(
                    '<i class="bi bi-trash"></i> Delete',
                    ['action' => 'delete', $vitalDataEntity->id],
                    [
                        'confirm' => 'Are you sure you want to delete this record?',
                        'class' => 'btn btn-delete px-3 py-2',
                        'escape' => false
                    ]
                ) ?>

            </div>

        </div>


        <h2 class="section-title mb-1">
            <i class="bi bi-clipboard2-pulse"></i> Respiratory Health
        </h2>
        <div class="section-underline"></div>
        <div class="detail-grid">

            <div class="vital-card accent-green d-flex align-items-center gap-3">
                <div class="icon-box">
                    <i class="bi bi-heart-pulse"></i>
                </div>
                <div>
                    <small class="text-muted d-block">Oxygen Saturation</small>
                    <div class="fw-semibold">
                        <?= $vitalDataEntity->oxygen_saturation ?>%
                    </div>
                </div>
            </div>

            <div class="vital-card accent-green d-flex align-items-center gap-3">
                <div class="icon-box">
                    <i class="bi bi-activity"></i>
                </div>
                <div>
                    <small class="text-muted d-block">Respiratory Rate</small>
                    <div class="fw-semibold">
                        <?= $vitalDataEntity->respiratory_rate ?> bpm
                    </div>
                </div>
            </div>

            <div class="vital-card accent-green d-flex align-items-center gap-3">
                <div class="icon-box">
                    <i class="bi bi-droplet"></i>
                </div>
                <div>
                    <small class="text-muted d-block">Pulse Oximetry</small>
                    <div class="fw-semibold">
                        <?= $vitalDataEntity->pulse_oximetry ?>%
                    </div>
                </div>
            </div>

            <div class="vital-card accent-green d-flex align-items-center gap-3">
                <div class="icon-box">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <div>
                    <small class="text-muted d-block">Weight</small>
                    <div class="fw-semibold">
                        <?= $vitalDataEntity->weight ?> kg
                    </div>
                </div>
            </div>

            <div class="vital-card accent-green d-flex align-items-center gap-3">
                <div class="icon-box">
                    <i class="bi bi-heart-pulse"></i>
                </div>
                <div>
                    <small class="text-muted d-block">Blood Pressure</small>
                    <div class="fw-semibold">
                        <?= h($vitalDataEntity->blood_pressure) ?>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <h2 class="section-title d-flex align-items-center gap-2">
            <i class="bi bi-activity"></i> Symptoms
        </h2>
        <div class="section-underline"></div>


        <?php if (!empty($vitalDataEntity->symptoms)): ?>

            <div class="detail-grid">

                <?php foreach ($vitalDataEntity->symptoms as $symptom): ?>
                    <?php
                    $severity = strtolower($symptom->_joinData->severity ?? 'unknown');

                    $badgeClass = match($severity) {
                        'mild' => 'severity-mild',
                        'moderate' => 'severity-moderate',
                        'severe' => 'severity-severe',
                        default => 'severity-unknown'
                    };
                    ?>


                    <div class="symptom-card accent-green d-flex justify-content-between align-items-center">
                                        <span class="symptom-name">
                                            <?= h($symptom->symptom_name) ?>
                                        </span>

                        <span class="severity-badge <?= $badgeClass ?>">
                                            <?= ucfirst($severity) ?>
                                        </span>
                    </div>

                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-muted mt-2">No symptoms recorded</div>
        <?php endif; ?>

    </div>
</div>
</div>
