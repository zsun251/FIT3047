<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
$this->assign('title', 'Add User');

// CSS
echo $this->Html->css('form-shared', ['block' => true]);
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
    ['title' => 'Users', 'url' => ['controller' => 'Users', 'action' => 'index']],
    ['title' => 'Add', 'url' => null]
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
            <h1><?= __('Add New User') ?></h1>
            <small class="text-muted">Create a new user account</small>
        </div>
    </div>

    <?= $this->Form->create($user) ?>

    <!-- Account Credentials -->
    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-key"></i> Account Credentials
        </h2>
        <div class="form-grid">
            <div class="form-group">
                <?= $this->Form->control('username', [
                    'label' => 'Username <span class="text-danger">*</span>',
                    'escape' => false,
                    'required' => true,
                    'minlength' => 3,
                    'maxlength' => 50,
                    'pattern' => '[A-Za-z0-9][A-Za-z0-9._-]*[A-Za-z0-9]',
                    'title' => 'Username must start and end with a letter or number. Can contain letters, numbers, dots, underscores, and hyphens. No consecutive special characters.',
                    'autocomplete' => 'username',
                    'class' => 'form-control',
                    'placeholder' => 'johndoe'
                ]) ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('password', [
                    'label' => 'Password <span class="text-danger">*</span>',
                    'escape' => false,
                    'type' => 'password',
                    'required' => true,
                    'minlength' => 8,
                    'maxlength' => 255,
                    'autocomplete' => 'new-password',
                    'class' => 'form-control',
                    'templateVars' => [
                        'help' => 'Minimum 8 characters with uppercase, lowercase, and number.'
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
                    'label' => 'First Name <span class="text-danger">*</span>',
                    'escape' => false,
                    'required' => true,
                    'minlength' => 2,
                    'maxlength' => 100,
                    'pattern' => "[A-Za-z]+([\' -][A-Za-z]+)*",
                    'title' => 'First name can only contain letters, spaces, hyphens, and apostrophes. Must start with a letter.',
                    'autocomplete' => 'given-name',
                    'class' => 'form-control',
                    'placeholder' => 'John'
                ]) ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('last_name', [
                    'label' => 'Last Name <span class="text-danger">*</span>',
                    'escape' => false,
                    'required' => true,
                    'minlength' => 2,
                    'maxlength' => 100,
                    'pattern' => "[A-Za-z]+([\' -][A-Za-z]+)*",
                    'title' => 'Last name can only contain letters, spaces, hyphens, and apostrophes. Must start with a letter.',
                    'autocomplete' => 'family-name',
                    'class' => 'form-control',
                    'placeholder' => 'Doe'
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
                    'label' => 'Email <span class="text-danger">*</span>',
                    'escape' => false,
                    'type' => 'email',
                    'required' => true,
                    'maxlength' => 255,
                    'autocomplete' => 'email',
                    'class' => 'form-control',
                    'placeholder' => 'john.doe@example.com'
                ]) ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('phone', [
                    'label' => 'Phone Number',
                    'type' => 'tel',
                    'required' => false,
                    'maxlength' => 20,
                    'pattern' => '[0-9 +]+',
                    'title' => 'Phone number can only contain numbers and spaces.',
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

    <!-- System Information -->
    <div class="form-section">
        <h2 class="form-section-title">
            <i class="bi bi-gear"></i> System Information
        </h2>
        <div class="form-grid">
            <div class="form-group">
                <?= $this->Form->control('type', [
                    'label' => 'User Type <span class="text-danger">*</span>',
                    'escape' => false,
                    'type' => 'select',
                    'options' => [
                        'admin' => 'Admin',
                        'clinician' => 'Clinician',
                        'patient' => 'Patient'
                    ],
                    'required' => true,
                    'class' => 'form-select',
                    'empty' => '-- Select User Type --'
                ]) ?>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> <?= __('Create User') ?>
        </button>

        <?= $this->Html->link(
            '<i class="bi bi-x-circle"></i> Cancel',
            ['action' => 'index'],
            ['class' => 'btn btn-outline-secondary', 'escape' => false]
        ) ?>
    </div>

    <?= $this->Form->end() ?>
</div>
