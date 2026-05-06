<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Clinician $clinician
 * @var string[]|\Cake\Collection\CollectionInterface $users
 * @var string[]|\Cake\Collection\CollectionInterface $patients
 */
$this->assign('title', 'Edit Clinician');

// CSS
echo $this->Html->css('form-shared', ['block' => true]);
?>

<!-- Back Link -->
<?= $this->Html->link(
    '<i class="bi bi-arrow-left"></i> Back to Clinicians',
    ['action' => 'index'],
    ['class' => 'back-link', 'escape' => false]
) ?>

<div class="form-container">
    <!-- Form Header -->
    <div class="form-header">
        <div>
            <h1><?= __('Edit Clinician') ?></h1>
            <?php if ($clinician->hasValue('user')): ?>
                <small class="text-muted">Update clinician information for <?= h($clinician->user->first_name . ' ' . $clinician->user->last_name) ?></small>
            <?php endif; ?>
        </div>
        <span class="badge-clinician">Clinician</span>
    </div>

    <?= $this->Form->create($clinician) ?>

    <!-- Clinician Information -->
    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-person-badge"></i> Clinician Information
        </h2>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Clinician ID</label>
                <div class="d-flex align-items-center"">
                <span class="badge bg-secondary fs-6 px-3 py-2">#<?= h($clinician->id) ?></span>
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

        <?php if ($clinician->hasValue('user')): ?>
            <div class="form-info-alert mt-3">
                <i class="bi bi-info-circle"></i>
                <div>
                    <strong>Associated User:</strong>
                    <?= h($clinician->user->first_name . ' ' . $clinician->user->last_name) ?>
                    (@<?= h($clinician->user->username) ?>)
                    <br>
                    <small class="text-muted">
                        To edit personal details, go to
                        <?= $this->Html->link('Users', ['controller' => 'Users', 'action' => 'edit', $clinician->user_id]) ?>
                    </small>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Assigned Patients -->
    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-people"></i> Assigned Patients
        </h2>
        <div class="form-group">
            <?= $this->Form->control('patients._ids', [
                'label' => 'Assigned Patients',
                'options' => $patients,
                'multiple' => 'checkbox',
                'class' => 'form-check-input',
                'templates' => [
                    'checkboxWrapper' => '<div class="form-check mb-2">{{label}}</div>',
                    'nestingLabel' => '<label class="form-check-label d-flex align-items-center gap-2">{{input}}{{text}}</label>',
                ],
                'templateVars' => [
                    'help' => 'Select which patients are assigned to this clinician. Only assigned patients will be visible to this clinician.'
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
        <?= $this->Form->postLink(
            '<i class="bi bi-trash"></i> Delete Clinician',
            ['action' => 'delete', $clinician->id],
            [
                'confirm' => __('Are you sure you want to delete clinician #{0}?', $clinician->id),
                'class' => 'btn btn-outline-danger ms-auto',
                'escape' => false
            ]
        ) ?>
    </div>

    <?= $this->Form->end() ?>
</div>
