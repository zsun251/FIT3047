<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\VitalData $vitalDataEntity
 * @var string[]|\Cake\Collection\CollectionInterface $patients
 * @var string[]|\Cake\Collection\CollectionInterface $symptoms
 */

$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
    ['title' => 'Health Records', 'url' => ['controller' => 'VitalData', 'action' => 'index']],
    ['title' => 'Edit', 'url' => null]
];

$selected = [];
$severityMap = [];

if (!empty($vitalDataEntity->symptoms)) {
    foreach ($vitalDataEntity->symptoms as $symptom) {
        $selected[$symptom->id] = true;
        $severityMap[$symptom->id] = $symptom->_joinData->severity ?? null;
    }
}
?>

<?= $this->element('page_header', [
    'breadcrumbs' => $breadcrumbs
]) ?>

<style>
    .health-record-edit .section-title {
        font-weight: 700;
        margin-bottom: .35rem;
    }

    .health-record-edit .section-underline {
        width: 40px;
        height: 3px;
        background: #0d6efd;
        border-radius: 2px;
        margin-bottom: 1rem;
    }

    .health-record-edit .symptom-card {
        border: 1px solid #e5e7eb;
        border-left: 4px solid #198754;
        border-radius: .75rem;
        background: #fff;
        padding: 1rem;
        height: 100%;
    }

    .health-record-edit .symptom-card .form-check-input {
        margin-top: 0;
        flex-shrink: 0;
    }

    .health-record-edit .symptom-label {
        font-weight: 500;
        margin-bottom: 0;
        line-height: 1.3;
    }

    .health-record-edit .symptom-row {
        display: grid;
        grid-template-columns: minmax(220px, 1fr) 220px;
        gap: 1rem;
        align-items: center;
    }

    .health-record-edit .symptom-check-wrap {
        display: flex;
        align-items: center;
        gap: .65rem;
        min-width: 0;
    }

    .health-record-edit .symptom-select-wrap .input {
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        .health-record-edit .symptom-row {
            grid-template-columns: 1fr;
            gap: .75rem;
        }
    }
</style>

<div class="container-fluid px-0 health-record-edit">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex justify-content-between align-items-start gap-3 p-4">
            <div>
                <h2 class="fw-bold mb-1">Edit Health Record</h2>
                <div class="section-underline"></div>
                <p class="text-muted mb-0">Update the patient’s vital signs and symptoms.</p>
            </div>

            <?= $this->Form->postLink(
                '<i class="bi bi-trash"></i> Delete',
                ['action' => 'delete', $vitalDataEntity->id],
                [
                    'confirm' => 'Are you sure you want to delete this record?',
                    'class' => 'btn btn-outline-danger rounded-pill px-4 py-2',
                    'escape' => false
                ]
            ) ?>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <?= $this->Form->create($vitalDataEntity, ['novalidate' => true]) ?>

            <div class="mb-4">
                <h4 class="section-title">Respiratory Health</h4>
                <div class="section-underline"></div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <?= $this->Form->control('patient_id', [
                        'options' => $patients,
                        'empty' => 'Select a patient',
                        'label' => 'Patient',
                        'class' => 'form-select'
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= $this->Form->control('oxygen_saturation', [
                        'type' => 'text',
                        'inputmode' => 'numeric',
                        'step' => '1',
                        'min' => 0,
                        'max' => 100,
                        'label' => 'Oxygen Saturation (%)',
                        'placeholder' => 'e.g. 90',
                        'class' => 'form-control',
                        'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= $this->Form->control('respiratory_rate', [
                        'type' => 'number',
                        'step' => '1',
                        'min' => 0,
                        'max' => 100,
                        'label' => 'Respiratory Rate (breaths/min)',
                        'placeholder' => 'e.g. 12',
                        'class' => 'form-control',
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= $this->Form->control('blood_pressure', [
                        'type' => 'text',
                        'label' => 'Blood Pressure (mmHg)',
                        'placeholder' => 'e.g. 120/80',
                        'class' => 'form-control',
                        'inputmode' => 'numeric',
                        'pattern' => '\d{1,3}/\d{1,3}',
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= $this->Form->control('pulse_oximetry', [
                        'type' => 'number',
                        'step' => '1',
                        'min' => 0,
                        'max' => 100,
                        'label' => 'Pulse Oximetry (%)',
                        'placeholder' => 'e.g. 98',
                        'class' => 'form-control',
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= $this->Form->control('weight', [
                        'type' => 'number',
                        'step' => '1',
                        'min' => 0,
                        'max' => 300,
                        'label' => 'Weight (kg)',
                        'placeholder' => 'e.g. 65.5',
                        'class' => 'form-control',
                    ]) ?>
                </div>
            </div>

            <hr class="my-4">

            <div class="mb-3">
                <h4 class="section-title"><i class="bi bi-activity text-primary me-2"></i>Symptoms</h4>
                <div class="section-underline"></div>
                <div class="alert alert-primary d-flex align-items-center gap-3 mb-0" role="alert">
                    <i class="bi bi-info-circle fs-5"></i>
                    <span>Select the symptoms experienced by the patient, then choose a severity for each selected symptom.</span>
                </div>
            </div>

            <div class="row g-3">
                <?php foreach ($symptoms as $id => $name): ?>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <div class="symptom-card">
                            <div class="symptom-row">
                                <div class="symptom-check-wrap">
                                    <?= $this->Form->checkbox("selected_symptoms.$id", [
                                        'value' => $id,
                                        'hiddenField' => false,
                                        'checked' => isset($selected[$id]),
                                        'class' => 'form-check-input',
                                        'id' => 'symptom-' . $id
                                    ]) ?>
                                    <label class="symptom-label" for="symptom-<?= h($id) ?>"><?= h($name) ?></label>
                                </div>

                                <div class="symptom-select-wrap">
                                    <?= $this->Form->control("symptom_severity.$id", [
                                        'type' => 'select',
                                        'options' => [
                                            'mild' => 'Mild',
                                            'moderate' => 'Moderate',
                                            'severe' => 'Severe'
                                        ],
                                        'empty' => 'Select severity',
                                        'label' => false,
                                        'error' => false,
                                        'value' => $severityMap[$id] ?? null,
                                        'class' => 'form-select form-select-sm',
                                        'templates' => [
                                            'inputContainer' => '{{content}}'
                                        ]
                                    ]) ?>
                                    <?= $this->Form->error("symptom_severity.$id") ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <?= $this->Html->link(
                    '<i class="bi bi-arrow-left"></i> Back',
                    ['action' => 'view', $vitalDataEntity->id],
                    ['class' => 'btn btn-outline-secondary rounded-pill px-4 py-2', 'escape' => false]
                ) ?>

                <?= $this->Form->button('<i class="bi bi-check-circle"></i> Update Entry', [
                    'class' => 'btn btn-primary rounded-pill px-4 py-2 fw-semibold',
                    'escapeTitle' => false
                ]) ?>
            </div>

            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
