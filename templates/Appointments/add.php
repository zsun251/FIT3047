<?php
declare(strict_types=1);

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Appointment $appointment
 * @var array<int, string> $patients
 * @var array<int, string> $clinicians
 */

$this->assign('title', 'Add Appointment');

echo $this->Html->css([
    'form-shared',
    'appointments',
], ['block' => true]);

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
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
    ['title' => 'Appointments', 'url' => ['controller' => 'Appointments', 'action' => 'index']],
    ['title' => 'Add', 'url' => null]
];
?>

<!-- Header Section -->
<?= $this->element('page_header', [
    'breadcrumbs' => $breadcrumbs
]) ?>


<div class="form-container">
    <div class="form-header">
        <div>
            <h1><?= __('Add New Appointment') ?></h1>
        </div>
    </div>

    <?= $this->Form->create($appointment) ?>

    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-people"></i> <?= __('Appointment Assignment') ?>
        </h2>

        <div class="form-grid">
            <div class="form-group">
                <?= $this->Form->control('patient_id', [
                    'label' => 'Patient <span class="text-danger">*</span>',
                    'escape' => false,
                    'type' => 'select',
                    'options' => $patients,
                    'required' => true,
                    'empty' => '-- Select Patient --',
                    'class' => 'form-select',
                ]) ?>
            </div>

            <div class="form-group">
                <?= $this->Form->control('clinician_id', [
                    'label' => 'Clinician <span class="text-danger">*</span>',
                    'escape' => false,
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
            <i class="bi bi-calendar2-week"></i> <?= __('Appointment Details') ?>
        </h2>

        <div class="appointment-schedule-label-grid">
            <div class="appointment-date-label">Appointment Date <span class="text-danger">*</span></div>
            <div class="appointment-duration-label">Duration <span class="text-danger">*</span></div>
            <div class="appointment-time-label">Appointment Time <span class="text-danger">*</span><span class="required"></span></div>
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
