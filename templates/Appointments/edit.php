<?php
declare(strict_types=1);

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Appointment $appointment
 * @var array<int, string>|\Cake\Collection\CollectionInterface $patients
 * @var array<int, string>|\Cake\Collection\CollectionInterface $clinicians
 * @var array<string, string> $appointmentStatusOptions
 */

$this->assign('title', 'Edit Appointment');

echo $this->Html->css([
    'form-shared',
    'appointments',
], ['block' => true]);

    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
        ['title' => 'Appointments', 'url' => ['controller' => 'Appointments', 'action' => 'index']],
        ['title' => 'Edit', 'url' => null]
    ];
?>

<!-- Header Section -->
<?= $this->element('page_header', [
    'breadcrumbs' => $breadcrumbs
]) ?>

<?= $this->Html->link(
    '<i class="bi bi-arrow-left"></i> Back to Appointments',
    ['action' => 'index'],
    ['class' => 'back-link', 'escape' => false]
) ?>

<div class="form-container appointments-page">
    <div class="form-header appointments-page-header">
        <div class="appointments-page-heading">
            <h1><?= __('Edit Appointment') ?></h1>
            <p class="appointments-page-subtitle">
                <?= __('Update the appointment details and status.') ?>
            </p>
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
            <i class="bi bi-calendar2-week"></i> <?= __('Appointment Details') ?>
        </h2>

        <div class="form-grid">
            <div class="form-group">
                <?= $this->Form->control('scheduled_at', [
                    'label' => 'Appointment Time',
                    'type' => 'datetime',
                    'required' => true,
                    'class' => 'form-control',
                ]) ?>
            </div>

            <div class="form-group">
                <?= $this->Form->control('duration', [
                    'label' => 'Duration',
                    'type' => 'number',
                    'required' => true,
                    'min' => 1,
                    'class' => 'form-control',
                ]) ?>
            </div>

            <div class="form-group">
                <?= $this->Form->control('status', [
                    'label' => 'Status',
                    'type' => 'select',
                    'options' => $appointmentStatusOptions,
                    'required' => true,
                    'class' => 'form-select',
                ]) ?>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> <?= __('Save Appointment') ?>
        </button>

        <?= $this->Form->postLink(
            '<i class="bi bi-trash"></i> Delete',
            ['action' => 'delete', $appointment->id],
            [
                'confirm' => __('Are you sure you want to delete appointment #{0}?', $appointment->id),
                'class' => 'btn btn-outline-danger',
                'escape' => false,
            ]
        ) ?>

        <?= $this->Html->link(
            '<i class="bi bi-x-circle"></i> Cancel',
            ['action' => 'index'],
            ['class' => 'btn btn-outline-secondary', 'escape' => false]
        ) ?>
    </div>

    <?= $this->Form->end() ?>
</div>
