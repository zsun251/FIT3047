<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\VitalData $vitalDataEntity
 * @var \Cake\Collection\CollectionInterface|string[] $patients
 * @var \Cake\Collection\CollectionInterface|string[] $symptoms
 */
?>

<?php
$identity = $this->request->getAttribute('identity');

$isStaff = false;
if ($identity) {
    $user = $identity->getOriginalData();
    $isStaff = !empty($user->is_staff);
}

$backText = $isStaff ? 'Back' : 'Back';

$backUrl = $this->request->referer(true) ?: (
$isStaff
    ? ['controller' => 'VitalData', 'action' => 'index']
    : '/'
);
?>

<div class="back-link">
    <?= $this->Html->link(
        '<i class="bi bi-arrow-left"></i><span>' . $backText . '</span>',
        $backUrl,
        ['escape' => false]
    ) ?>
</div>

<div class="container-fluid px-4">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3 px-4">
            <h2 class="fw-bold mb-1">Add New Entry</h2>
            <div style="width: 40px; height: 3px; background-color: #0d6efd; border-radius: 2px; margin-bottom: 10px;"></div>

            <p class="text-muted mb-3">
                Record the patient’s vital signs and symptoms.
            </p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <?= $this->Form->create($vitalDataEntity, ['novalidate' => true]) ?>

            <div class="mb-4">
                <h4 class="fw-bold mb-1">Vital Signs</h4>
                <div style="width: 40px; height: 3px; background-color: #0d6efd; border-radius: 2px; margin-bottom: 12px;"></div>
            </div>

            <?php
                if (!$isStaff && $patientId) {
                    echo $this->Form->control('patient_id', [
                        'type' => 'hidden',
                        'value' => $patientId
                    ]);
                }

                if ($isStaff) {
                    echo $this->Form->control('patient_id', [
                        'options' => $patients,
                        'empty' => 'Select a patient Id',
                        'class' => 'form-select mb-3'
                    ]);
                }
                echo $this->Form->control('oxygen_saturation', [
                    'type' => 'text',
                    'inputmode' => 'numeric',
                    'step' => '1',
                    'min' => 70,
                    'max' => 100,
                    'label' => 'Oxygen Saturation (%)',
                    'placeholder' => 'e.g. 90',
                    'class' => 'form-control mb-3',
                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                    ]);

                    echo $this->Form->control('respiratory_rate', [
                        'type' => 'number',
                        'step' => '1',
                        'min' => 8,
                        'max' => 40,
                        'label' => 'Respiratory Rate (breaths/min)',
                        'placeholder' => 'e.g. 12',
                        'class' => 'form-control mb-3',
                    ]);
                    //echo $this->Form->control('lung_function');
                    echo $this->Form->control('blood_pressure', [
                        'type' => 'text',
                        'label' => 'Blood Pressure (mmHg)',
                        'placeholder' => 'e.g. 120/80',
                        'class' => 'form-control mb-3',
                        'inputmode' => 'numeric',
                        'pattern' => '\d{2,3}/\d{2,3}',
                        ]);
                    echo $this->Form->control('pulse_oximetry', [
                        'type' => 'number',
                        'step' => '1',
                        'min' => 70,
                        'max' => 100,
                        'label' => 'Pulse Oximetry (%)',
                        'placeholder' => 'e.g. 98',
                        'class' => 'form-control mb-3',
                    ]);
                    echo $this->Form->control('weight', [
                        'type' => 'number',
                        'step' => '1',
                        'min' => 0,
                        'max' => 300,
                        'label' => 'Weight (kg)',
                        'placeholder' => 'e.g. 65.5',
                        'class' => 'form-control mb-3',
                    ]);
                   // echo $this->Form->control('symptoms._ids', [
                        //'options' => $symptoms,
                        //'multiple' => 'checkbox',
                        //'label' => 'Select Symptoms'
                    //]);
                ?>
            <div class="mt-4 pt-2">
                <div class="mb-3">
                    <h4 class="fw-bold mb-1">Symptoms</h4>
                    <div style="width: 40px; height: 3px; background-color: #0d6efd; border-radius: 2px; margin-bottom: 12px;"></div>
                    <p class="text-muted mb-0">Choose any symptoms experienced and set their severity.</p>
                </div>

                <?php foreach ($symptoms as $id => $name): ?>
                    <div class="border rounded-3 px-3 py-2 mb-2 bg-light">
                        <div class="row align-items-center w-100">
                        <div class="col-md-7 d-flex align-items-center gap-3 py-1">
                            <?= $this->Form->checkbox("selected_symptoms.$id", [
                                'value' => $id,
                                'hiddenField' => false,
                                'class' => 'form-check-input mt-0'
                            ]) ?>
                            <label class="mb-0 fw-medium"><?= h($name) ?></label>
                        </div>

                            <div class="col-md-5 d-flex align-items-center justify-content-end">
                                <div class="mb-0 w-100">
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
                                        'class' => 'form-select form-select-sm mb-0',
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

            <?php
            echo $this->Form->control('recorded_at', [
                'class' => 'form-control mb-3',
                'label' => 'Recorded At'
            ]);
                ?>
            <div class="d-flex justify-content-end mt-4 pt-2 border-top">
                <?= $this->Form->button(__('Submit Entry'), [
                    'class' => 'btn btn-primary rounded-pill px-4 py-2 fw-semibold'
                ]) ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
