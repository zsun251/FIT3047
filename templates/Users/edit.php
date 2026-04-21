<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var bool $isPatientSelfEdit
 * @var int|null $adminId
 * @var int|null $clinicianId
 * @var int|null $patientId
 */
$this->assign('title', $isPatientSelfEdit ? 'My Profile' : 'Edit User');

// CSS
echo $this->Html->css('form-shared', ['block' => true]);
echo $this->Html->css('user-view', ['block' => true]);

// Type badge class
$typeBadgeClass = 'badge-' . strtolower($user->type);
?>

<!-- Back Link -->
<?php if (!$isPatientSelfEdit): ?>
    <?= $this->Html->link(
        '<i class="bi bi-arrow-left"></i> Back to Users',
        ['action' => 'index'],
        ['class' => 'back-link', 'escape' => false]
    ) ?>
<?php else: ?>
    <?= $this->Html->link(
        '<i class="bi bi-arrow-left"></i> Back to Dashboard',
        ['controller' => 'Dashboard', 'action' => 'patient'],
        ['class' => 'back-link', 'escape' => false]
    ) ?>
<?php endif; ?>

<div class="form-container">
    <!-- Form Header -->
    <div class="form-header">
        <div>
            <h1><?= $isPatientSelfEdit ? __('Edit My Profile') : __('Edit User') ?></h1>
            <?php if (!$isPatientSelfEdit): ?>
                <small class="text-muted">Update user information for <?= h($user->username) ?></small>
            <?php endif; ?>
        </div>
        <?php if (!$isPatientSelfEdit): ?>
            <span class="<?= $typeBadgeClass ?>"><?= h(ucfirst($user->type)) ?></span>
        <?php endif; ?>
    </div>

    <?php if ($isPatientSelfEdit): ?>
        <div class="form-info-alert">
            <i class="bi bi-info-circle"></i>
            <p>You can update your personal information below. For security reasons, please ensure your contact details are current.</p>
        </div>
    <?php endif; ?>

    <?= $this->Form->create($user) ?>

    <!-- Account Credentials -->
    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-key"></i> Account Credentials
        </h2>
        <div class="form-grid">
            <div class="form-group">
                <?= $this->Form->control('username', [
                    'label' => 'Username',
                    'required' => true,
                    'minlength' => 3,
                    'maxlength' => 50,
                    'pattern' => '[A-Za-z0-9][A-Za-z0-9._-]*[A-Za-z0-9]',
                    'title' => 'Username must start and end with a letter or number. Can contain letters, numbers, dots, underscores, and hyphens. No consecutive special characters.',
                    'autocomplete' => 'username',
                    'class' => 'form-control',
                ]) ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('password', [
                    'label' => 'Password',
                    'type' => 'password',
                    'value' => '',
                    'required' => false,
                    'minlength' => 8,
                    'maxlength' => 255,
                    'autocomplete' => 'new-password',
                    'class' => 'form-control',
                    'placeholder' => 'Leave blank to keep current password',
                    'templateVars' => [
                        'help' => 'Minimum 8 characters with uppercase, lowercase, and number. Leave blank to keep current password.'
                    ]
                ]) ?>
            </div>
        </div>
    </div>

    <!-- Personal Information -->
    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-person"></i> Personal Information
        </h2>
        <div class="form-grid">
            <div class="form-group">
                <?= $this->Form->control('first_name', [
                    'label' => 'First Name',
                    'required' => true,
                    'minlength' => 2,
                    'maxlength' => 100,
                    'pattern' => "[A-Za-z]+([\' -][A-Za-z]+)*",
                    'title' => 'First name can only contain letters, spaces, hyphens, and apostrophes. Must start with a letter.',
                    'autocomplete' => 'given-name',
                    'class' => 'form-control',
                ]) ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('last_name', [
                    'label' => 'Last Name',
                    'required' => true,
                    'minlength' => 2,
                    'maxlength' => 100,
                    'pattern' => "[A-Za-z]+([\' -][A-Za-z]+)*",
                    'title' => 'Last name can only contain letters, spaces, hyphens, and apostrophes. Must start with a letter.',
                    'autocomplete' => 'family-name',
                    'class' => 'form-control',
                ]) ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('date_of_birth', [
                    'label' => 'Date of Birth',
                    'empty' => true,
                    'required' => false,
                    'min' => '1900-01-01',
                    'max' => date('Y-m-d'),
                    'class' => 'form-control',
                ]) ?>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-envelope"></i> Contact Information
        </h2>
        <div class="form-grid">
            <div class="form-group">
                <?= $this->Form->control('email', [
                    'label' => 'Email Address',
                    'type' => 'email',
                    'required' => true,
                    'maxlength' => 255,
                    'autocomplete' => 'email',
                    'class' => 'form-control',
                    'placeholder' => 'your.email@example.com'
                ]) ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('phone', [
                    'label' => 'Phone Number',
                    'type' => 'tel',
                    'required' => false,
                    'minlength' => 10,
                    'maxlength' => 15,
                    'pattern' => '0[0-9]{9}',
                    'title' => 'Australian phone number: 10 digits starting with 0 (e.g., 0412345678). Spaces will be removed automatically.',
                    'autocomplete' => 'tel',
                    'class' => 'form-control',
                    'placeholder' => '0412 345 678'
                ]) ?>
            </div>
            <div class="form-group form-grid-full">
                <?= $this->Form->control('address', [
                    'label' => 'Address',
                    'type' => 'text',
                    'required' => false,
                    'minlength' => 5,
                    'maxlength' => 500,
                    'autocomplete' => 'street-address',
                    'placeholder' => '123 Example Street, Suburb VIC 3000',
                    'class' => 'form-control',
                ]) ?>
            </div>
        </div>
    </div>

    <!-- System Information (Staff Only) -->
    <?php if (!$isPatientSelfEdit): ?>
        <div class="form-section">
            <h2 class="form-section-title">
                <i class="bi bi-gear"></i> System Information
            </h2>
            <div class="form-grid">
                <div class="form-group">
                    <?= $this->Form->control('type', [
                        'label' => 'User Type',
                        'type' => 'select',
                        'options' => [
                            'admin' => 'Admin',
                            'clinician' => 'Clinician',
                            'patient' => 'Patient'
                        ],
                        'disabled' => true,
                        'class' => 'form-select',
                        'templateVars' => [
                            'help' => 'User type cannot be changed after creation to prevent data loss.'
                        ]
                    ]) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('created_at', [
                        'label' => 'Created At',
                        'disabled' => true,
                        'class' => 'form-control',
                    ]) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Form Actions -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> <?= __('Save Changes') ?>
        </button>

        <?php if ($isPatientSelfEdit): ?>
            <?= $this->Html->link(
                '<i class="bi bi-x-circle"></i> Cancel',
                ['controller' => 'Dashboard', 'action' => 'patient'],
                ['class' => 'btn btn-outline-secondary', 'escape' => false]
            ) ?>
        <?php else: ?>
            <?= $this->Html->link(
                '<i class="bi bi-x-circle"></i> Cancel',
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escape' => false]
            ) ?>
            <?= $this->Form->postLink(
                '<i class="bi bi-trash"></i> Delete User',
                ['action' => 'delete', $user->id],
                [
                    'confirm' => __('Are you sure you want to delete {0}?', $user->username),
                    'class' => 'btn btn-outline-danger ms-auto',
                    'escape' => false
                ]
            ) ?>
        <?php endif; ?>
    </div>

    <?= $this->Form->end() ?>
</div>
