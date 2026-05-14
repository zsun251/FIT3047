<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Patient $patient
 * @var string[]|\Cake\Collection\CollectionInterface $users
 * @var string[]|\Cake\Collection\CollectionInterface $clinicians
 */

use App\Policy\UserAccess;

$this->assign('title', 'Edit Patient');

// CSS
echo $this->Html->css('form-shared', ['block' => true]);
$identity = $this->request->getAttribute('identity');

$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
    ['title' => 'Patients', 'url' => ['controller' => 'Patients', 'action' => 'index']],
    ['title' => 'Edit', 'url' => null]
];
?>

<!-- Header Section -->
<?= $this->element('page_header', [
    'breadcrumbs' => $breadcrumbs
]) ?>


<div class="form-container">
    <!-- Form Header -->
    <div class="form-header">
        <div>
            <h1><?= __('Edit Patient') ?></h1>
            <?php if ($patient->hasValue('user')): ?>
                <small class="text-muted">Update patient information for <?= h($patient->user->first_name . ' ' . $patient->user->last_name) ?></small>
            <?php endif; ?>
        </div>
        <span class="badge-patient">Patient</span>
    </div>

    <?= $this->Form->create($patient) ?>

    <!-- Patient Information -->
    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-person-badge"></i> Patient Information
        </h2>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Patient ID</label>
                <div class="d-flex align-items-center"">
                    <span class="badge bg-secondary fs-6 px-3 py-2">#<?= h($patient->id) ?></span>
                </div>
            </div>

            <div class="form-group">
                <?= $this->Form->control('user_id', [
                    'label' => 'Associated User Account',
                    'options' => $users,
                    'class' => 'form-select',
                    'required' => true,
                    'disabled' => true,
                    'templateVars' => [
                        'help' => 'The user account cannot be changed after creation to prevent data loss.'
                    ]
                ]) ?>
            </div>
        </div>

        <?php if ($patient->hasValue('user')): ?>
            <div class="form-info-alert mt-3">
                <i class="bi bi-info-circle"></i>
                <div>
                    <strong>Associated User:</strong>
                    <?= h($patient->user->first_name . ' ' . $patient->user->last_name) ?>
                    (@<?= h($patient->user->username) ?>)
                    <br>
                    <small class="text-muted">
                        To edit personal details, go to
                        <?= $this->Html->link('Users', ['controller' => 'Users', 'action' => 'edit', $patient->user_id]) ?>
                    </small>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Assigned Clinicians -->
    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-people"></i> Assigned Clinicians
        </h2>
        <div class="form-group">
            <?= $this->Form->control('clinicians._ids', [
                'label' => 'Assigned Clinicians',
                'options' => $clinicians,
                'multiple' => 'checkbox',
                'class' => 'form-check-input me-2',
                'templateVars' => [
                    'help' => 'Select which clinicians are assigned to this patient. Only assigned clinicians can view and manage this patient\'s records.'
                ]
            ]) ?>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> <?= __('Save Changes') ?>
        </button>
        <?= $this->Html->link(
            '<i class="bi bi-x-circle"></i> Cancel',
            ['action' => 'index'],
            ['class' => 'btn btn-outline-secondary', 'escape' => false]
        ) ?>

<!--        --><?php //if (UserAccess::isAdmin($identity)): ?>
<!--            --><?php //= $this->Form->postLink(
//                '<i class="bi bi-trash"></i> Delete Patient',
//                ['action' => 'delete', $patient->id],
//                [
//                    'confirm' => __('Are you sure you want to delete patient #{0}?', $patient->id),
//                    'class' => 'btn btn-outline-danger ms-auto',
//                    'escape' => false
//                ]
//            ) ?>
<!--        --><?php //endif; ?>
    </div>

    <?= $this->Form->end() ?>
