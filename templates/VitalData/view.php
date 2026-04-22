<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\VitalData $vitalDataEntity
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <?= $this->Html->link(__('Edit Vital Data'), ['action' => 'edit', $vitalDataEntity->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Vital Data'), ['action' => 'delete', $vitalDataEntity->id], ['confirm' => __('Are you sure you want to delete # {0}?', $vitalDataEntity->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Vital Data'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Vital Data'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="vitalData view content">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-3">
                    <h2 class="fw-bold mb-1">Vital Data Record</h2>
                    <p class="text-muted mb-0">
                        Patient
                        <?= $vitalDataEntity->hasValue('patient') ? h($vitalDataEntity->patient->id) : 'N/A' ?>
                        • Recorded
                        <?= h($vitalDataEntity->recorded_at) ?>
                    </p>
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
                            <div class="p-2 bg-primary-subtle rounded-3 h-100">
                                <small class="text-muted d-block mb-1">Oxygen Saturation</small>
                                <div class="fw-semibold">
                                    <?= $vitalDataEntity->oxygen_saturation ?>%
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-2 bg-primary-subtle rounded-3 h-100">
                                <small class="text-muted d-block mb-1">Respiratory Rate</small>
                                <div class="fw-semibold">
                                    <?= $vitalDataEntity->respiratory_rate ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-2 bg-primary-subtle rounded-3 h-100">
                                <small class="text-muted d-block mb-1">Pulse Oximetry</small>
                                <div class="fw-semibold">
                                    <?= $vitalDataEntity->pulse_oximetry ?>%
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-2 bg-primary-subtle rounded-3 h-100">
                                <small class="text-muted d-block mb-1">Weight</small>
                                <div class="fw-semibold">
                                    <?= $vitalDataEntity->weight ?> kg
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-2 bg-primary-subtle rounded-3 h-100">
                                <small class="text-muted d-block mb-1">Blood Pressure</small>
                                <div class="fw-semibold">
                                    <?= h($vitalDataEntity->blood_pressure) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="card shadow-sm border-0 mt-2">
                        <div class="card-body p-3">
                            <h5 class="fw-semibold mb-1">Symptoms</h5>
                            <div style="width: 40px; height: 3px; background-color: #0d6efd; border-radius: 2px; margin-bottom: 12px;"></div>

                            <?php if (!empty($vitalDataEntity->symptoms)): ?>
                                <div class="row g-3">

                                    <?php foreach ($vitalDataEntity->symptoms as $symptom): ?>
                                        <div class="col-md-6">
                                            <div class="p-2 bg-primary-subtle rounded-3 h-100">

                                                <small class="text-muted d-block mb-1">
                                                    <?= h($symptom->symptom_name) ?>
                                                </small>

                                                <div class="fw-semibold">
                                                    <?= !empty($symptom->_joinData->severity)
                                                        ? h(ucfirst($symptom->_joinData->severity))
                                                        : 'N/A' ?>
                                                </div>

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
