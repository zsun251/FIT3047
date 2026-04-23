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
<div class="row">
    <aside class="column">
        <div class="side-nav-modern">

            <?= $this->Html->link(
                '<i class="bi bi-pencil-square"></i> Edit Vital Data',
                ['action' => 'edit', $vitalDataEntity->id],
                ['class' => 'side-nav-item', 'escape' => false]
            ) ?>

            <?= $this->Form->postLink(
                '<i class="bi bi-trash"></i> Delete Vital Data',
                ['action' => 'delete', $vitalDataEntity->id],
                [
                    'confirm' => __('Are you sure you want to delete # {0}?', $vitalDataEntity->id),
                    'class' => 'side-nav-item',
                    'escape' => false
                ]
            ) ?>

            <?= $this->Html->link(
                '<i class="bi bi-list"></i> List Vital Data',
                ['action' => 'index'],
                ['class' => 'side-nav-item', 'escape' => false]
            ) ?>

            <?= $this->Html->link(
                '<i class="bi bi-plus-circle"></i> New Vital Data',
                ['action' => 'add'],
                ['class' => 'side-nav-item', 'escape' => false]
            ) ?>

        </div>
    </aside>
    <div class="column column-80">
        <div class="vitalData view content">
            <?php
            $name = "Patient " . ($vitalDataEntity->patient->id ?? '');
            $words = explode(" ", $name);
            $initials = strtoupper($words[0][0] . ($words[1][0] ?? ''));
            ?>
            <div class="card shadow-sm border-0 mb-4 vital-header-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">

                        <?php
                        $patientName = 'Patient ' . ($vitalDataEntity->patient->id ?? '');
                        $initials = strtoupper(substr($patientName, 0, 1));
                        ?>

                        <?php
                        $patientName = 'Patient ' . ($vitalDataEntity->patient->id ?? '');
                        $initials = strtoupper(substr($patientName, 0, 1));
                        ?>

                        <?php
                        $patientName = 'Patient ' . ($vitalDataEntity->patient->id ?? '');
                        $initials = strtoupper(substr($patientName, 0, 1));
                        ?>

                        <?php
                        $patientName = 'Patient ' . ($vitalDataEntity->patient->id ?? '');
                        $initials = strtoupper(substr($patientName, 0, 1));
                        ?>

                        <div class="profile-avatar">
                            <?= $initials ?>
                        </div>

                        <div>
                            <h2 class="fw-bold mb-1">
                                <?= $patientName ?>
                            </h2>
                            <p class="text-muted mb-0">
                                Patient ID: <?= $vitalDataEntity->patient->id ?? 'N/A' ?>
                                &nbsp;&nbsp;•&nbsp;&nbsp;
                                Recorded: <?= h($vitalDataEntity->recorded_at) ?>
                            </p>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <h5 class="fw-semibold mb-2 position-relative">
                        Vital Signs
                    </h5>
                    <div style="width: 40px; height: 3px; background-color: #0d6efd; border-radius: 2px; margin-bottom: 10px;"></div>

                    <div class="row g-3">

                        <div class="col-md-6">
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
                        </div>

                        <div class="col-md-6">
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
                        </div>

                        <div class="col-md-6">
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
                        </div>

                        <div class="col-md-6">
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
                        </div>

                        <div class="col-md-6">
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
                    </div>
                    <hr class="my-4">
                    <h5 class="fw-semibold mb-2">Symptoms</h5>
                    <div style="width: 40px; height: 3px; background-color: #0d6efd; border-radius: 2px; margin-bottom: 18px;"></div>

                    <?php if (!empty($vitalDataEntity->symptoms)): ?>
                        <div class="row g-3">

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

                                <div class="col-md-6">
                                    <div class="symptom-card accent-green d-flex justify-content-between align-items-center">
                                        <span class="symptom-name">
                                            <?= h($symptom->symptom_name) ?>
                                        </span>

                                        <span class="severity-badge <?= $badgeClass ?>">
                                            <?= ucfirst($severity) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">No symptoms recorded</span>
                            <?php endif; ?>

                        </div>
                    </div>
    </div>
</div>
