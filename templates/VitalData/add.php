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

    <div class="wizard-header mb-4">
        <div class="wizard-decor decor-plus">+</div>
        <div class="wizard-decor decor-circle"></div>
        <div class="wizard-decor decor-heart">♡</div>

        <div class="wizard-steps">
            <div class="wizard-step active" data-step-indicator="1">
                <div class="wizard-number">1</div>
                <div class="wizard-label">Vitals</div>
            </div>

            <div class="wizard-line"></div>

            <div class="wizard-step" data-step-indicator="2">
                <div class="wizard-number">2</div>
                <div class="wizard-label">Symptoms</div>
            </div>

            <div class="wizard-line"></div>

            <div class="wizard-step" data-step-indicator="3">
                <div class="wizard-number">3</div>
                <div class="wizard-label">Review</div>
            </div>
        </div>

        <div class="wizard-content p-5">

            <?= $this->Form->create($vitalDataEntity, ['novalidate' => true]) ?>

            <div class="form-step" data-step="1">

                <div class="mb-4 d-flex align-items-start gap-4">
                    <div class="section-icon">
                        <i class="bi bi-activity"></i>
                    </div>

                    <div>
                        <h4 class="fw-bold mb-1">Vital Signs</h4>
                        <p class="text-primary fw-semibold mb-2">Step 1 of 3</p>

                    <div style="width: 40px; height: 3px; background: linear-gradient(90deg,#4f6df5,#7b4bb7); border-radius: 2px; margin-bottom: 12px;"></div>

                        <p class="text-muted mb-0 section-description">
                            Enter the patient’s vital sign readings.
                        </p>
                </div>
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
            ?>

                <div class="vital-row">

                    <div class="vital-icon">
                        <i class="bi bi-droplet-half"></i>
                    </div>

                    <div class="vital-label">
                        <label>Oxygen Saturation (%)</label>
                    </div>

                    <div class="vital-input">

                        <?= $this->Form->control('oxygen_saturation', [
                            'type' => 'text',
                            'inputmode' => 'numeric',
                            'step' => '1',
                            'min' => 70,
                            'max' => 100,
                            'label' => false,
                            'placeholder' => 'e.g. 90',
                            'class' => 'form-control',
                            'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                        ]) ?>

                        <span class="input-unit">%</span>

                    </div>

                </div>

                <div class="vital-row">

                    <div class="vital-icon">
                        <i class="bi bi-wind"></i>
                    </div>

                    <div class="vital-label">
                        <label>Respiratory Rate (breaths/min)</label>
                    </div>

                    <div class="vital-input">

                        <?= $this->Form->control('respiratory_rate', [
                            'type' => 'text',
                            'inputmode' => 'numeric',
                            'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                            'step' => '1',
                            'min' => 8,
                            'max' => 40,
                            'label' => false,
                            'placeholder' => 'e.g. 12',
                            'class' => 'form-control',
                        ]) ?>

                        <span class="input-unit">breaths/min</span>

                    </div>

                </div>

                <div class="vital-row">

                    <div class="vital-icon">
                        <i class="bi bi-heart-pulse"></i>
                    </div>

                    <div class="vital-label">
                        <label>Blood Pressure (mmHg)</label>
                    </div>

                    <div class="vital-input">

                        <?= $this->Form->control('blood_pressure', [
                            'type' => 'text',
                            'label' => false,
                            'placeholder' => 'e.g. 120/80',
                            'class' => 'form-control',
                            'inputmode' => 'numeric',
                            'pattern' => '\d{2,3}/\d{2,3}',
                        ]) ?>

                        <span class="input-unit">mmHg</span>

                    </div>

                </div>

                <div class="vital-row">

                    <div class="vital-icon">
                        <i class="bi bi-activity"></i>
                    </div>

                    <div class="vital-label">
                        <label>Pulse Oximetry (%)</label>
                    </div>

                    <div class="vital-input">

                        <?= $this->Form->control('pulse_oximetry', [
                            'type' => 'text',
                            'inputmode' => 'numeric',
                            'label' => false,
                            'placeholder' => 'e.g. 98',
                            'class' => 'form-control',
                            'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                        ]) ?>

                        <span class="input-unit">%</span>

                    </div>

                </div>

                <div class="vital-row">

                    <div class="vital-icon">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>

                    <div class="vital-label">
                        <label>Weight (kg)</label>
                    </div>

                    <div class="vital-input">

                        <?= $this->Form->control('weight', [
                            'type' => 'text',
                            'inputmode' => 'decimal',
                            'label' => false,
                            'placeholder' => 'e.g. 65.5',
                            'class' => 'form-control',
                        ]) ?>

                        <span class="input-unit">kg</span>

                    </div>

                </div>

                <?php
                ?>

                <div class="d-flex justify-content-end mt-4 next-btn-wrapper">
                <button type="button"
                        class="next-btn next-step">
                    Next
                </button>
            </div>

            </div>

        <div class="form-step d-none" data-step="2">

            <div>

                <div class="mb-4 d-flex align-items-start gap-4">

                    <div class="section-icon">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>

                    <div>
                        <h4 class="fw-bold mb-1">Symptoms</h4>

                        <p class="text-primary fw-semibold mb-2">
                            Step 2 of 3
                        </p>

                        <div style="width: 40px; height: 3px; background: linear-gradient(90deg,#4f6df5,#7b4bb7); border-radius: 2px; margin-bottom: 12px;"></div>

                        <p class="text-muted mb-0 section-description">
                            Choose any symptoms experienced and set their severity.
                        </p>
                    </div>

                </div>

                <?php foreach ($symptoms as $id => $name): ?>
                    <div class="symptom-option">
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
            <div class="d-flex justify-content-between align-items-end mt-3 pt-3">

                <button type="button"
                        class="back-btn prev-step">
                    Back
                </button>

                <button type="button"
                        class="next-btn next-step">
                    Next
                </button>

            </div>

            </div>

        <div class="form-step d-none" data-step="3">

            <div class="mb-4">
                <h4 class="fw-bold mb-1">Step 3: Review & Submit</h4>

                <div style="width: 40px; height: 3px; background-color: #0d6efd; border-radius: 2px; margin-bottom: 12px;"></div>

                <p class="text-muted mb-0">
                    Review the entered information before submitting.
                </p>
            </div>

            <div class="alert alert-info border-0 shadow-sm">
                Please ensure all vital signs and symptoms have been entered correctly before submitting this health record.
            </div>

            <div class="review-grid">
                <div class="review-card">
                    <h5>Vital Signs Summary</h5>
                    <div id="review-vitals-list"></div>
                </div>

                <div class="review-card">
                    <h5>Symptoms Summary</h5>
                    <div id="review-symptoms-list"></div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 pt-2">

                <button type="button"
                        class="back-btn prev-step">
                    Back
                </button>

                <button type="submit"
                        class="next-btn">
                    Submit
                </button>

            </div>

        </div>

            <?= $this->Form->end() ?>
        </div>
    </div>

<style>
    .wizard-header {
        position: relative;

        background: transparent
        border-radius: 0px;
        padding: 38px 30px 33px;

        box-shadow: none

        overflow: hidden;
    }

    .wizard-header::before {
        content: "";

        position: absolute;
        inset: 0;

        background:
            radial-gradient(circle at 85% 20%, rgba(255,255,255,0.18), transparent 18%),
            radial-gradient(circle at 92% 42%, rgba(255,255,255,0.12), transparent 12%);

        pointer-events: none;
    }

    .wizard-steps {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 14px;
        position: relative;
        z-index: 2;
    }

    .wizard-step {
        text-align: center;
        color: #1e293b;
        font-weight: 700;
    }

    .vital-input input[type="number"]::-webkit-inner-spin-button,
    .vital-input input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .vital-input input[type="number"] {
        -moz-appearance: textfield;
    }

    .wizard-number {
        width: 58px;
        height: 58px;
        border-radius: 50%;

        background: #eef4ff;
        border: 2px solid #dbe7ff;
        color: white;

        display: flex;
        align-items: center;
        justify-content: center;

        margin: 0 auto 8px;

        font-size: 24px;
        font-weight: 800;
        margin-top: -30px;
    }

    .wizard-step.active .wizard-number {
        background: linear-gradient(
            135deg,
            #2563eb 0%,
            #4f6df5 40%,
            #7c3aed 100%
        );

        box-shadow:
            0 0 0 8px rgba(255,255,255,0.16),
            0 8px 24px rgba(79,109,245,0.22);
    }

    .wizard-line {
        width: 140px;
        height: 3px;
        background: #dbe7ff;
        margin-bottom: 34px;
    }

    .section-icon {
        width: 60px;
        height: 60px;
        min-width: 60px;
        border-radius: 16px;
        background: linear-gradient(
            135deg,
            #4f6df5,
            #7b4bb7
        );
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        box-shadow: 0 10px 20px rgba(124, 58, 237, 0.18);
    }

    .wizard-step.active {
        color: #2563eb;
    }

    .wizard-decor {
        position: absolute;
        color: rgba(255,255,255,0.35);
        font-size: 32px;
        font-weight: 700;
    }

    .decor-plus {
        left: 120px;
        top: 35px;
    }

    .decor-circle {
        left: 70px;
        top: 90px;
        width: 18px;
        height: 18px;
        border: 4px solid rgba(255,255,255,0.3);
        border-radius: 50%;
    }

    .decor-heart {
        right: 110px;
        top: 62px;
        font-size: 48px;
    }

    .wizard-content {
        background: linear-gradient(
            135deg,
            #e7f0ff 0%,
            #efe7ff 100%
        );

        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);

        border: 1px solid rgba(255,255,255,0.4);

        border-radius: 22px;
        margin-top: 40px;
        position: relative;
        z-index: 2;
    }

    .form-step {
        position: relative;
        z-index: 2;
        padding-top: 0;
    }

    .text-primary {
        background: linear-gradient(
            90deg,
            #4f6df5,
            #7b4bb7
        );

        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .vital-row {
        display: grid;
        grid-template-columns: 70px 220px 1fr;
        align-items: center;
        gap: 18px;
        margin-bottom: 24px;
    }

    .vital-icon {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        background: #f4efff;
        color: #7c3aed;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
    }

    .vital-label label {
        font-weight: 600;
        font-size: 15px;
        color: #1e293b;
        margin-bottom: 0;
    }

    .vital-input {
        position: relative;
    }

    .vital-input .input {
        margin-bottom: 0;
    }

    .vital-input .form-control {
        height: 52px;
        border-radius: 14px;
        padding-left: 18px;
        padding-right: 65px;
        font-size: 16px;
        border: 1px solid #e5e7eb;
    }

    .input-unit {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        background: #f4efff;
        color: #7c3aed;
        padding: 5px 10px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
    }
    .next-btn {
        background: linear-gradient(
            135deg,
            #4f6df5,
            #6c4df6
        );
        border: none;
        color: white;

        width: 125px !important;
        height: 44px !important;
        min-width: 150px !important;

        padding: 0 !important;

        font-size: 16px;
        font-weight: 700;
        border-radius: 999px;

        box-shadow: 0 8px 20px rgba(91, 46, 255, 0.25);
        transition: all 0.2s ease;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-left: auto;
        margin-top: 0;
        line-height: 1;
        margin-bottom: 35px;
    }

    .next-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(91, 46, 255, 0.45);
    }

    .wizard-content {
        padding-bottom: 25px !important;
    }

    .form-step {
        padding-bottom: 0 !important;
        margin-bottom: 0 !important;
    }

    .next-btn {
        min-width: 140px;
        height: 64px;
        font-size: 18px;
        margin-top: 0;
        margin-bottom: 0;
    }

    .d-flex.justify-content-end.mt-4 {
        margin-top: 15px !important;
        margin-bottom: 0 !important;
    }

    .wizard-container {
        padding-bottom: 10px !important;
        min-height: auto !important;
    }

    .wizard-content {
        margin-bottom: 0 !important;
        padding-bottom: 10px !important;
    }

    .wizard-card {
        padding: 50px 55px 30px 55px !important;
        border-radius: 40px;
        height: auto !important;
        min-height: unset !important;
    }

    .form-step {
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }

    .next-btn-wrapper {
        padding-bottom: 20px;
    }

    .back-link {
        margin-left: 30px;
        margin-top: 12px;
        margin-bottom: 0px;
    }

    .back-link a {
        display: inline-flex;
        align-items: center;
        gap: 10px;

        color: #6b7280;
        text-decoration: none;

        font-size: 16px;
        font-weight: 600;
    }

    .back-link i {
        font-size: 20px;
    }

    .section-description {
        margin-left: -78px;
    }

    .back-btn {
        background: #ece8ff;
        border: none;
        color: #6a42f4;

        width: 120px;
        height: 44px;

        border-radius: 999px;

        font-size: 16px;
        font-weight: 600;

        cursor: pointer;

        display: flex;
        align-items: center;
        justify-content: center;

        box-shadow: 0 4px 12px rgba(106, 66, 244, 0.12);
    }

    .back-btn:hover {
        background: #ddd6ff;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(106, 66, 244, 0.18);
        transition: all 0.2s ease;
    }

    .next-btn-wrapper,
    .d-flex.justify-content-between.align-items-end.mt-3.pt-3 {
        margin-bottom: 20px !important;
    }

    .step-icon-box {
        width: 60px;
        height: 60px;
        min-width: 60px;

        border-radius: 16px;

        background: linear-gradient(
            135deg,
            #4f6df5,
            #7b4bb7
        );

        color: white;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 28px;

        box-shadow: 0 10px 20px rgba(124, 58, 237, 0.18);
    }

    .step-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1e293b;
    }

    .step-subtitle {
        font-size: 1rem;
        font-weight: 700;

        background: linear-gradient(
            90deg,
            #4f6df5,
            #7b4bb7
        );

        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;

        margin-bottom: 6px;
    }

    .step-line {
        width: 58px;
        height: 4px;

        border-radius: 999px;

        background: linear-gradient(
            90deg,
            #4f6df5,
            #7b4bb7
        );

        margin-bottom: 18px;
    }

    .step-description {
        font-size: 1rem;
        color: #4b5563;
        margin-bottom: 0;
    }

    .symptom-option {
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 18px 22px;
        margin-bottom: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        position: relative;

        transition: 0.2s ease;
    }

    .symptom-option:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
    }

    .symptom-option label {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
    }

    .symptom-option .form-check-input {
        width: 22px;
        height: 22px;
        border-radius: 7px;
        border: 2px solid #d6dbe7;
        cursor: pointer;
    }

    .symptom-option .form-check-input:checked {
        background-color: #6d28d9;
        border-color: #6d28d9;
    }

    .symptom-option .form-select {
        height: 48px;
        border-radius: 14px;
        border: 1px solid #d8dee9;
        font-weight: 600;
        color: #334155;
        padding-left: 16px;
    }

    .symptom-option .form-select:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.12);
    }

    .symptom-option .row {
        min-height: 56px;
    }

    .review-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-top: 28px;
    }

    .review-card {
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }

    .review-card h5 {
        font-size: 1.25rem;
        font-weight: 800;
        margin-bottom: 18px;
        color: #1e293b;
    }

    .review-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;

        padding: 12px 0;
        border-bottom: 1px solid #e5e7eb;

        font-size: 1rem;
    }

    .review-item:last-child {
        border-bottom: none;
    }

    .review-item span {
        color: #475569;
        font-weight: 600;
    }

    .review-item strong {
        color: #111827;
        font-weight: 800;
        text-align: right;
    }

    .form-control.is-invalid,
    .was-validated .form-control:invalid {
        background-image: none !important;
        padding-right: 20px !important;
    }

    .vital-row {
        align-items: center;
    }

    .vital-input {
        position: relative;
    }

    .vital-input .error-message {
        position: absolute;
        top: 100%;
        left: 0;
        margin-top: 6px;
        color: #dc3545;
        font-size: 0.85rem;
    }

    .severity-dropdown-wrapper {
        position: relative;
    }

    .severity-dropdown-wrapper .form-select.is-invalid {
        border: 2px solid #dc3545 !important;
        color: #dc3545 !important;
        padding-right: 55px;
    }

    .severity-dropdown-wrapper .form-select.is-invalid option {
        color: #212529;
    }

    .severity-dropdown-wrapper .form-select.is-invalid:focus {
        box-shadow: 0 0 0 0.2rem rgba(220,53,69,.15);
    }

    .severity-dropdown-wrapper .form-select.is-invalid::placeholder {
        color: #dc3545 !important;
    }

    .symptom-option .severity-dropdown-wrapper .form-select.is-invalid {
        border-color: #dc3545 !important;
        color: #dc3545 !important;
    }

    .symptom-option .severity-dropdown-wrapper .error-message {
        color: #dc3545 !important;
        font-size: 0.9rem;
        margin-top: 8px;
        padding-left: 4px;
    }

</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let currentStep = 1;

        const steps = document.querySelectorAll('.form-step');
        const indicators = document.querySelectorAll('.wizard-step');

        function showStep(step) {
            steps.forEach(function (section) {
                section.classList.add('d-none');
            });

            document.querySelector('[data-step="' + step + '"]').classList.remove('d-none');

            indicators.forEach(function (indicator) {
                indicator.classList.remove('active');
            });

            document.querySelector('[data-step-indicator="' + step + '"]').classList.add('active');
        }

        document.querySelectorAll('.next-step').forEach(function (button) {
            button.addEventListener('click', function () {
                if (currentStep < 3) {

                    if (!validateStep(currentStep)) {
                        return;
                    }

                    currentStep++;

                    if (currentStep === 3) {
                        updateReview();
                    }

                    showStep(currentStep);
                }
            });
        });

        document.querySelectorAll('.prev-step').forEach(function (button) {
            button.addEventListener('click', function () {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
        });

        function clearFieldError(input) {
            const wrapper = input.closest('.vital-input');

            if (!wrapper) return;

            const existingError = wrapper.querySelector('.error-message');

            if (existingError) {
                existingError.remove();
            }

            input.classList.remove('is-invalid');
        }

        function showFieldError(input, message) {

            if (!input) return;

            clearFieldError(input);

            const wrapper = input.closest('.severity-dropdown-wrapper') || input.parentElement;

            const error = document.createElement('div');

            error.className = 'error-message text-danger';
            error.innerText = message;

            wrapper.appendChild(error);

            input.classList.add('is-invalid');
        }

        function validateStep(step) {
            let isValid = true;

            if (step === 1) {
                const oxygen = document.querySelector('[name="oxygen_saturation"]');
                const respiratory = document.querySelector('[name="respiratory_rate"]');
                const bloodPressure = document.querySelector('[name="blood_pressure"]');
                const pulse = document.querySelector('[name="pulse_oximetry"]');
                const weight = document.querySelector('[name="weight"]');

                [oxygen, respiratory, bloodPressure, pulse, weight].forEach(clearFieldError);

                if (oxygen.value === '') {
                    showFieldError(oxygen, 'Please enter oxygen saturation.');
                    isValid = false;
                } else if (isNaN(oxygen.value) || oxygen.value < 0 || oxygen.value > 100) {
                    showFieldError(oxygen, 'Oxygen saturation must be between 0 and 100%.');
                    isValid = false;
                }

                if (respiratory.value === '') {
                    showFieldError(respiratory, 'Please enter respiratory rate.');
                    isValid = false;
                } else if (isNaN(respiratory.value) || respiratory.value < 0 || respiratory.value > 100) {
                    showFieldError(respiratory, 'Respiratory rate must be between 0 and 100.');
                    isValid = false;
                }

                if (bloodPressure.value === '') {
                    showFieldError(bloodPressure, 'Please enter blood pressure.');
                    isValid = false;
                } else if (!/^\d{1,3}\/\d{1,3}$/.test(bloodPressure.value)) {
                    showFieldError(bloodPressure, 'Blood pressure must be in the format 120/80.');
                    isValid = false;
                } else {
                    const parts = bloodPressure.value.split('/');
                    const systolic = Number(parts[0]);
                    const diastolic = Number(parts[1]);

                    if (systolic < 0 || systolic > 300 || diastolic < 0 || diastolic > 300) {
                        showFieldError(bloodPressure, 'Blood pressure must be between 0/0 and 300/300.');
                        isValid = false;
                    }
                }

                if (pulse.value === '') {
                    showFieldError(pulse, 'Please enter pulse oximetry.');
                    isValid = false;
                } else if (isNaN(pulse.value) || pulse.value < 0 || pulse.value > 100) {
                    showFieldError(pulse, 'Pulse oximetry must be between 0 and 100%.');
                    isValid = false;
                }

                if (weight.value === '') {
                    showFieldError(weight, 'Please enter weight.');
                    isValid = false;
                } else if (isNaN(weight.value) || weight.value < 0 || weight.value > 300) {
                    showFieldError(weight, 'Weight must be between 0 and 300kg.');
                    isValid = false;
                }
            }

            if (step === 2) {

                const selectedSymptoms = document.querySelectorAll('input[name^="selected_symptoms"]:checked');

                selectedSymptoms.forEach(function (checkbox) {

                    const symptomId = checkbox.value;

                    const severity = document.querySelector(
                        `[name="symptom_severity[${symptomId}]"]`
                    );

                    clearFieldError(severity);

                    if (!severity || severity.value === '') {

                        showFieldError(
                            severity,
                            'Please select a severity.'
                        );

                        isValid = false;
                    }
                });
            }

            return isValid;
        }

        function updateReview() {
            const vitalsList = document.getElementById('review-vitals-list');
            const symptomsList = document.getElementById('review-symptoms-list');

            vitalsList.innerHTML = `
        <div class="review-item"><span>Oxygen Saturation</span><strong>${document.querySelector('[name="oxygen_saturation"]').value || 'Not entered'}%</strong></div>
        <div class="review-item"><span>Respiratory Rate</span><strong>${document.querySelector('[name="respiratory_rate"]').value || 'Not entered'} breaths/min</strong></div>
        <div class="review-item"><span>Blood Pressure</span><strong>${document.querySelector('[name="blood_pressure"]').value || 'Not entered'} mmHg</strong></div>
        <div class="review-item"><span>Pulse Oximetry</span><strong>${document.querySelector('[name="pulse_oximetry"]').value || 'Not entered'}%</strong></div>
        <div class="review-item"><span>Weight</span><strong>${document.querySelector('[name="weight"]').value || 'Not entered'} kg</strong></div>
    `;

            symptomsList.innerHTML = '';

            document.querySelectorAll('input[name^="selected_symptoms"]:checked').forEach(function (checkbox) {
                const symptomId = checkbox.value;
                const label = checkbox.closest('.row').querySelector('label').innerText;
                const severity = document.querySelector(`[name="symptom_severity[${symptomId}]"]`)?.value || 'No severity selected';

                symptomsList.innerHTML += `
            <div class="review-item">
                <span>${label}</span>
                <strong>${severity}</strong>
            </div>
        `;
            });

            if (symptomsList.innerHTML === '') {
                symptomsList.innerHTML = `<p class="text-muted mb-0">No symptoms selected.</p>`;
            }
        }

        showStep(currentStep);
    });
</script>
