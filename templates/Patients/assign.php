<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Clinician|null $clinician
 * @var string[]|\Cake\Collection\CollectionInterface $clinicians
 * @var string[]|\Cake\Collection\CollectionInterface $patients
 * @var int[]|null $assignedPatientIds
 */

use App\Policy\UserAccess;

$this->assign('title', 'Assign Patients');

// CSS
echo $this->Html->css('form-shared', ['block' => true]);
$identity = $this->request->getAttribute('identity');
?>

<!-- Back Link -->
<?= $this->Html->link(
    '<i class="bi bi-arrow-left"></i> Back',
    $this->request->referer() ?: ['controller' => 'Clinicians', 'action' => 'index'],
    ['class' => 'back-link', 'escape' => false]
) ?>

<div class="form-container">
    <!-- Form Header -->
    <div class="form-header">
        <div>
            <?php if (UserAccess::isAdmin($identity)) : ?>
                <h1><?= __('Assign Patients to Clinician') ?></h1>
                <?php if ($clinician && $clinician->hasValue('user')): ?>
                    <small class="text-muted">Assign patients to <?= h($clinician->user->first_name . ' ' . $clinician->user->last_name) ?></small>
                <?php else: ?>
                    <small class="text-muted">Select a clinician and assign their patients</small>
                <?php endif; ?>
            <?php else: ?>
                <h1><?= __('Manage My Patients') ?></h1>
                <small class="text-muted">Assign patients to your account</small>
            <?php endif; ?>
        </div>
        <span class="badge-clinician">Patient Assignment</span>
    </div>

    <?= $this->Form->create(null, ['type' => 'post']) ?>

    <!-- Select Clinician (if not provided) -->
    <?php if (!$clinician): ?>
        <div class="form-section">
            <h2 class="form-section-title">
                <i class="bi bi-person-badge"></i> Select Clinician
            </h2>
            <div class="form-grid">
                <div class="form-group">
                    <?= $this->Form->control('clinician_id', [
                        'label' => 'Clinician <span class="text-danger">*</span>',
                        'escape' => false,
                        'options' => $clinicians,
                        'empty' => 'Select a clinician',
                        'class' => 'form-select',
                        'required' => true,
                        'onchange' => 'this.form.submit()',
                        'templateVars' => [
                            'help' => 'Select a clinician to manage their patient assignments'
                        ]
                    ]) ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?= $this->Form->hidden('clinician_id', ['value' => $clinician->id]) ?>

        <!-- Clinician Information -->
        <div class="form-section">
            <h2 class="form-section-title">
                <i class="bi bi-person-badge"></i> Clinician Information
            </h2>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Clinician</label>
                    <div class="form-control-plaintext">
                        <?php if ($clinician->hasValue('user')): ?>
                            <strong>
                                <?= h($clinician->user->first_name . ' ' . $clinician->user->last_name) ?>
                            </strong>
                            <span class="text-muted">(@<?= h($clinician->user->username) ?>)</span>
                        <?php else: ?>
                            <strong>Clinician #<?= h($clinician->id) ?></strong>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign Patients -->
        <div class="form-section">
            <h2 class="form-section-title">
                <i class="bi bi-people"></i> Assign Patients
            </h2>
            <div class="form-group">
                <?= $this->Form->control('patient_ids', [
                    'label' => 'Select Patients to Assign',
                    'options' => $patients,
                    'multiple' => 'checkbox',
                    'value' => $assignedPatientIds,
                    'class' => 'form-check-input',
                    'templates' => [
                        'checkboxWrapper' => '<div class="form-check mb-2">{{label}}</div>',
                        'nestingLabel' => '<label class="form-check-label d-flex align-items-center gap-2">{{input}}{{text}}</label>',
                    ],
                    'templateVars' => [
                        'help' => 'Select all patients that should be assigned to this clinician. Uncheck to remove assignment.'
                    ]
                ]) ?>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> <?= __('Save Assignments') ?>
            </button>
            <?= $this->Html->link(
                '<i class="bi bi-x-circle"></i> Cancel',
                ['controller' => 'Clinicians', 'action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escape' => false]
            ) ?>
        </div>
    <?php endif; ?>

    <?= $this->Form->end() ?>
</div>
