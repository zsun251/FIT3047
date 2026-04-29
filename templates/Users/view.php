<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var int|null $adminId
 * @var int|null $clinicianId
 * @var int|null $patientId
 */
$this->assign('title', 'User Details');

// CSS
echo $this->Html->css('view-shared', ['block' => true]);
echo $this->Html->css('user-view', ['block' => true]);

// Get user initials for avatar
$initials = strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1));

// Type badge class
$typeBadgeClass = 'badge-' . strtolower($user->type);
?>

<!-- Back Link -->
<?= $this->Html->link(
    '<i class="bi bi-arrow-left"></i> Back to Users',
    ['action' => 'index'],
    ['class' => 'back-link', 'escape' => false]
) ?>

<div class="view-container">
    <!-- Header -->
    <div class="view-header">
        <div class="user-header">
            <div class="user-avatar"><?= $initials ?></div>
            <div class="user-info">
                <h1><?= h($user->first_name . ' ' . $user->last_name) ?></h1>
                <div class="user-meta">
                    <span class="user-meta-item">
                        <i class="bi bi-at"></i> <?= h($user->username) ?>
                    </span>
                    <span class="<?= $typeBadgeClass ?>"><?= h(ucfirst($user->type)) ?></span>
                    <span class="user-meta-item">
                        <i class="bi bi-hash"></i> ID: <?= $this->Number->format($user->id) ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="view-actions">
            <?= $this->Html->link(
                '<i class="bi bi-pencil"></i> Edit',
                ['action' => 'edit', $user->id],
                ['class' => 'btn btn-primary', 'escape' => false]
            ) ?>
            <?= $this->Form->postLink(
                '<i class="bi bi-trash"></i> Delete',
                ['action' => 'delete', $user->id],
                [
                    'confirm' => __('Are you sure you want to delete {0}?', $user->username),
                    'class' => 'btn btn-outline-danger',
                    'escape' => false
                ]
            ) ?>
        </div>
    </div>

    <!-- Account Information -->
    <h2 class="section-title mb-3">
        <i class="bi bi-person-badge"></i> Account Information
    </h2>
    <div class="detail-grid">
        <div class="detail-item <?= 'user-' . strtolower($user->type) ?>">
            <div class="detail-label">
                <i class="bi bi-calendar-plus"></i> Created
            </div>
            <div class="detail-value">
                <?= h($user->created_at->format('M d, Y')) ?>
                <small class="text-muted d-block"><?= h($user->created_at->diffForHumans()) ?></small>
            </div>
        </div>

        <div class="detail-item <?= 'user-' . strtolower($user->type) ?>">
            <div class="detail-label">
                <i class="bi bi-person-badge"></i> Account Type
            </div>
            <div class="detail-value">
                <span class="<?= $typeBadgeClass ?>"><?= h(ucfirst($user->type)) ?></span>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <h2 class="section-title mb-3 mt-4">
        <i class="bi bi-envelope"></i> Contact Information
    </h2>
    <div class="detail-grid">
        <div class="detail-item <?= 'user-' . strtolower($user->type) ?>">
            <div class="detail-label">
                <i class="bi bi-envelope"></i> Email
            </div>
            <div class="detail-value">
                <a href="mailto:<?= h($user->email) ?>" class="email-link">
                    <?= h($user->email) ?>
                </a>
            </div>
        </div>

        <div class="detail-item <?= 'user-' . strtolower($user->type) ?>">
            <div class="detail-label">
                <i class="bi bi-telephone"></i> Phone
            </div>
            <div class="detail-value <?= $user->phone ? '' : 'text-muted' ?>">
                <?= $user->phone ? h($user->phone) : 'Not provided' ?>
            </div>
        </div>
    </div>

    <!-- Personal Information -->
    <h2 class="section-title mb-3 mt-4">
        <i class="bi bi-person"></i> Personal Information
    </h2>
    <div class="detail-grid">
        <div class="detail-item <?= 'user-' . strtolower($user->type) ?>">
            <div class="detail-label">
                <i class="bi bi-cake"></i> Date of Birth
            </div>
            <div class="detail-value <?= $user->date_of_birth ? '' : 'text-muted' ?>">
                <?= $user->date_of_birth ? h($user->date_of_birth->format('M d, Y')) : 'Not provided' ?>
            </div>
        </div>

        <div class="detail-item <?= 'user-' . strtolower($user->type) ?>">
            <div class="detail-label">
                <i class="bi bi-geo-alt"></i> Address
            </div>
            <div class="detail-value <?= $user->address ? '' : 'text-muted' ?>">
                <?= $user->address ? h($user->address) : 'Not provided' ?>
            </div>
        </div>
    </div>

    <!-- Related Admin -->
    <?php if ($user->type === 'admin' && $adminId): ?>
        <div class="related-section">
            <h2>
                <i class="bi bi-shield-check"></i> Admin Profile
            </h2>
            <div class="related-items">
                <div class="related-item">
                    <div class="related-item-info">
                        <div>
                            <div class="related-item-title">System Administrator</div>
                            <div class="related-item-subtitle">
                                Full system access and user management permissions
                            </div>
                        </div>
                    </div>
                    <div class="related-item-actions">
<!--                        --><?php //= $this->Html->link(
//                            '<i class="bi bi-arrow-right-circle"></i> View Full Profile',
//                            ['controller' => 'Admins', 'action' => 'view', $adminId],
//                            ['class' => 'btn btn-sm btn-primary', 'escape' => false]
//                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Related Clinician -->
    <?php if ($user->type === 'clinician' && $clinicianId): ?>
        <div class="related-section">
            <h2>
                <i class="bi bi-clipboard2-pulse"></i> Clinician Profile
            </h2>
            <div class="related-items">
                <div class="related-item">
                    <div class="related-item-info">
                        <div>
                            <div class="related-item-title">Medical Professional</div>
                            <div class="related-item-subtitle">
                                Authorized to manage patients, appointments, and medical resources
                            </div>
                        </div>
                    </div>
                    <div class="related-item-actions">
<!--                        --><?php //= $this->Html->link(
//                            '<i class="bi bi-arrow-right-circle"></i> View Full Profile',
//                            ['controller' => 'Clinicians', 'action' => 'view', $clinicianId],
//                            ['class' => 'btn btn-sm btn-primary', 'escape' => false]
//                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Related Patient -->
    <?php if ($user->type === 'patient' && $patientId): ?>
        <div class="related-section">
            <h2>
                <i class="bi bi-person-heart"></i> Patient Profile
            </h2>
            <div class="related-items">
                <div class="related-item">
                    <div class="related-item-info">
                        <div>
                            <div class="related-item-title">Patient Record</div>
                            <div class="related-item-subtitle">
                                Access medical history, vital signs, and appointment records
                            </div>
                        </div>
                    </div>
                    <div class="related-item-actions">
                        <?= $this->Html->link(
                            '<i class="bi bi-arrow-right-circle"></i> View Full Profile',
                            ['controller' => 'Patients', 'action' => 'view', $patientId],
                            ['class' => 'btn btn-sm btn-primary', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
