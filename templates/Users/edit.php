<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var bool $isPatientSelfEdit
 * @var int|null $adminId
 * @var int|null $clinicianId
 * @var int|null $patientId
 */

use App\Policy\UserAccess;

$this->assign('title', $isPatientSelfEdit ? 'My Profile' : 'Edit User');

// CSS
echo $this->Html->css('form-shared', ['block' => true]);
echo $this->Html->css('view-shared', ['block' => true]);
echo $this->Html->css('user-view', ['block' => true]);

$typeBadgeClass = 'badge-' . strtolower($user->type);

$identity = $this->request->getAttribute('identity');
$isStaff = UserAccess::isStaff($identity);

// Get initials
$initials = strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1));

$breadcrumbs = [];

if ($isStaff) {
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
        ['title' => 'Users', 'url' => ['controller' => 'Users', 'action' => 'index']],
        ['title' => h($user->username), 'url' => ['controller' => 'Users', 'action' => 'view', $user->id]],
        ['title' => 'Edit User', 'url' => null]
    ];
} else {
    $breadcrumbs = [
        ['title' => 'Profile', 'url' => ['controller' => 'Users', 'action' => 'view', $user->id]],
        ['title' => 'Edit Profile', 'url' => null]
    ];
}
?>

<?= $this->element('page_header', [
    'breadcrumbs' => $breadcrumbs
]) ?>

<div class="form-container">

    <!-- Header -->
    <div class="view-header mb-4">
        <div class="user-header">
            <div class="user-avatar">
                <?= $initials ?>
            </div>

            <div class="user-info">
                <div style="display:flex; align-items:center; gap:.625rem; margin-bottom:.75rem; flex-wrap:wrap;">
                    <span class="type-badge type-article" style="position:static;">
                        <i class="bi bi-pencil-square"></i>
                        <?= $isPatientSelfEdit ? 'Edit Profile' : 'Edit User' ?>
                    </span>

                    <?php if (!$isPatientSelfEdit): ?>
                        <span class="<?= $typeBadgeClass ?>">
                            <?= h(ucfirst($user->type)) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <h1><?= h($user->first_name . ' ' . $user->last_name) ?></h1>

                <div class="user-meta">
                    <span class="user-meta-item">
                        <i class="bi bi-at"></i>
                        <?= h($user->username) ?>
                    </span>

                    <span class="user-meta-item">
                        <i class="bi bi-hash"></i>
                        ID: <?= $this->Number->format($user->id) ?>
                    </span>
                </div>

                <?php if ($isPatientSelfEdit): ?>
                    <small class="text-muted d-block mt-2">
                        Update your personal information and contact details below.
                    </small>
                <?php endif; ?>
            </div>
        </div>

        <div class="view-actions">
            <?= $this->Html->link(
                '<i class="bi bi-arrow-left"></i> Back to Profile',
                ['controller' => 'Users', 'action' => 'view', $user->id],
                ['class' => 'btn btn-outline-secondary', 'escape' => false]
            ) ?>
        </div>
    </div>

    <?php if ($user->getErrors()): ?>
        <div class="alert alert-danger mb-4" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill"></i> Could not save — please fix the following:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($user->getErrors() as $field => $errors): ?>
                    <?php foreach ((array)$errors as $msg): ?>
                        <li><strong><?= h(ucwords(str_replace('_', ' ', $field))) ?>:</strong> <?= h($msg) ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
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
                    'class' => 'form-control',
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
                    'class' => 'form-control',
                ]) ?>
            </div>

            <div class="form-group">
                <?= $this->Form->control('last_name', [
                    'label' => 'Last Name',
                    'required' => true,
                    'class' => 'form-control',
                ]) ?>
            </div>

            <div class="form-group">
                <?= $this->Form->control('date_of_birth', [
                    'label' => 'Date of Birth',
                    'empty' => true,
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
                    'class' => 'form-control',
                ]) ?>
            </div>

            <div class="form-group">
                <?= $this->Form->control('phone', [
                    'label' => 'Phone Number',
                    'type' => 'tel',
                    'class' => 'form-control',
                ]) ?>
            </div>

            <div class="form-group form-grid-full">
                <?= $this->Form->control('address', [
                    'label' => 'Address',
                    'type' => 'text',
                    'class' => 'form-control',
                ]) ?>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i>
            <?= __('Save Changes') ?>
        </button>

        <?= $this->Html->link(
            '<i class="bi bi-x-circle"></i> Cancel',
            ['controller' => 'Users', 'action' => 'view', $user->id],
            ['class' => 'btn btn-outline-secondary', 'escape' => false]
        ) ?>
    </div>

    <?= $this->Form->end() ?>
</div>
