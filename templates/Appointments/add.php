<?php
declare(strict_types=1);

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Appointment $appointment
 * @var array<int, string> $patients
 * @var array<int, string> $clinicians
 */

$this->assign('title', 'Add Appointment');

echo $this->Html->css('form-shared', ['block' => true]);

$durationOptions = [
    15 => '15 minutes',
    30 => '30 minutes',
    45 => '45 minutes',
];

$hourOptions = [];
for ($hour = 1; $hour <= 12; $hour++) {
    $hourOptions[(string)$hour] = (string)$hour;
}

$minuteOptions = [
    '00' => '00',
    '15' => '15',
    '30' => '30',
    '45' => '45',
];

$meridiemOptions = [
    'AM' => 'AM',
    'PM' => 'PM',
];

$today = date('Y-m-d');
$maxDate = date('Y-m-d', strtotime('+1 year'));

$scheduledDateValue = (string)$this->request->getData(
    'scheduled_date',
    !empty($appointment->scheduled_at) ? $appointment->scheduled_at->format('Y-m-d') : ''
);

$scheduledHourValue = (string)$this->request->getData(
    'scheduled_hour',
    !empty($appointment->scheduled_at) ? $appointment->scheduled_at->format('g') : ''
);

$scheduledMinuteValue = (string)$this->request->getData(
    'scheduled_minute',
    !empty($appointment->scheduled_at) ? $appointment->scheduled_at->format('i') : ''
);

$scheduledMeridiemValue = (string)$this->request->getData(
    'scheduled_meridiem',
    !empty($appointment->scheduled_at) ? $appointment->scheduled_at->format('A') : ''
);

$durationValue = $this->request->getData('duration', $appointment->duration ?? null);

$scheduledAtErrors = (array)$appointment->getError('scheduled_at');
$scheduledAtErrorMessage = '';

if (!empty($scheduledAtErrors)) {
    $scheduledAtErrorMessage = trim(implode(' ', array_map(
        static fn($error): string => is_string($error) ? $error : '',
        $scheduledAtErrors
    )));

    if ($scheduledAtErrorMessage === '') {
        $scheduledAtErrorMessage = 'Please enter a valid appointment date and time.';
    }
}
?>

<style>
    /* ============================================================================
       APPOINTMENT ADD PAGE LAYOUT
       Keep the schedule inputs aligned to the same width and height.
       ========================================================================== */
    .appointment-schedule-label-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 1rem;
        align-items: end;
        margin-bottom: 0.5rem;
    }

    .appointment-schedule-input-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 1rem;
        align-items: start;
    }

    .appointment-schedule-label-grid > div,
    .appointment-schedule-input-grid > div {
        min-width: 0;
    }

    .appointment-date-label,
    .appointment-time-label,
    .appointment-duration-label {
        font-size: 1rem;
        color: var(--c-text, #334155);
    }

    .appointment-date-label,
    .appointment-duration-label {
        font-weight: 700;
    }

    .appointment-time-label {
        grid-column: 3 / span 3;
        font-weight: 700;
    }

    .appointment-schedule-input-grid .form-group {
        margin-bottom: 0;
        min-width: 0;
    }

    .appointment-schedule-input-grid .form-select,
    .appointment-schedule-input-grid .form-control {
        width: 100%;
        min-height: 56px;
    }

    @media (max-width: 991.98px) {
        .appointment-schedule-label-grid,
        .appointment-schedule-input-grid {
            grid-template-columns: 1fr;
        }

        .appointment-time-label {
            grid-column: auto;
        }
    }
</style>

<?= $this->Html->link(
    '<i class="bi bi-arrow-left"></i> Back to Appointments',
    ['action' => 'index'],
    ['class' => 'back-link', 'escape' => false]
) ?>

<div class="form-container">
    <div class="form-header">
        <div>
            <h1><?= __('Add New Appointment') ?></h1>
        </div>
    </div>

    <?= $this->Form->create($appointment) ?>
    <?= $this->Form->hidden('status', ['value' => 'scheduled']) ?>

    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-people"></i> <?= __('Appointment Assignment') ?>
        </h2>

        <div class="form-grid">
            <div class="form-group">
                <?= $this->Form->control('patient_id', [
                    'label' => 'Patient',
                    'type' => 'select',
                    'options' => $patients,
                    'required' => true,
                    'empty' => '-- Select Patient --',
                    'class' => 'form-select',
                ]) ?>
            </div>

            <div class="form-group">
                <?= $this->Form->control('clinician_id', [
                    'label' => 'Clinician',
                    'type' => 'select',
                    'options' => $clinicians,
                    'required' => true,
                    'empty' => '-- Select Clinician --',
                    'class' => 'form-select',
                ]) ?>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-calendar2-week"></i> <?= __('Schedule Details') ?>
        </h2>

        <div class="appointment-schedule-label-grid">
            <div class="appointment-date-label">Appointment Date</div>
            <div class="appointment-duration-label">Duration</div>
            <div class="appointment-time-label">Appointment Time <span class="required"></span></div>
        </div>

        <div class="appointment-schedule-input-grid">
            <div class="form-group">
                <?= $this->Form->control('scheduled_date', [
                    'label' => false,
                    'type' => 'date',
                    'required' => true,
                    'class' => 'form-control',
                    'value' => $scheduledDateValue,
                    'min' => $today,
                    'max' => $maxDate,
                    'aria-label' => 'Appointment Date',
                ]) ?>
            </div>

            <div class="form-group">
                <?= $this->Form->control('duration', [
                    'label' => false,
                    'type' => 'select',
                    'options' => $durationOptions,
                    'required' => true,
                    'empty' => '-- Select Duration --',
                    'class' => 'form-select',
                    'value' => $durationValue,
                    'aria-label' => 'Duration',
                ]) ?>
            </div>

            <div class="form-group">
                <?= $this->Form->control('scheduled_hour', [
                    'label' => false,
                    'type' => 'select',
                    'options' => $hourOptions,
                    'required' => true,
                    'empty' => '-- Hour --',
                    'class' => 'form-select',
                    'value' => $scheduledHourValue,
                    'aria-label' => 'Hour',
                ]) ?>
            </div>

            <div class="form-group">
                <?= $this->Form->control('scheduled_minute', [
                    'label' => false,
                    'type' => 'select',
                    'options' => $minuteOptions,
                    'required' => true,
                    'empty' => '-- Minute --',
                    'class' => 'form-select',
                    'value' => $scheduledMinuteValue,
                    'aria-label' => 'Minute',
                ]) ?>
            </div>

            <div class="form-group">
                <?= $this->Form->control('scheduled_meridiem', [
                    'label' => false,
                    'type' => 'select',
                    'options' => $meridiemOptions,
                    'required' => true,
                    'empty' => '-- AM/PM --',
                    'class' => 'form-select',
                    'value' => $scheduledMeridiemValue,
                    'aria-label' => 'AM or PM',
                ]) ?>
            </div>
        </div>

        <?php if (!empty($scheduledAtErrorMessage)): ?>
            <div class="error-message">
                <?= h($scheduledAtErrorMessage) ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> <?= __('Create Appointment') ?>
        </button>

        <?= $this->Html->link(
            '<i class="bi bi-x-circle"></i> Cancel',
            ['action' => 'index'],
            ['class' => 'btn btn-outline-secondary', 'escape' => false]
        ) ?>
    </div>

    <?= $this->Form->end() ?>
</div>
