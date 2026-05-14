<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Patient $patient
 */

use App\Policy\UserAccess;

$this->assign('title', 'Patient Details');

// CSS
echo $this->Html->css('view-shared', ['block' => true]);
echo $this->Html->css('user-view', ['block' => true]);

// Get user initials for avatar
$initials = 'PT';
if ($patient->hasValue('user')) {
    $initials = strtoupper(
        substr($patient->user->first_name, 0, 1) .
        substr($patient->user->last_name, 0, 1)
    );
}
$identity = $this->request->getAttribute('identity');
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
    ['title' => 'Patients', 'url' => ['controller' => 'Patients', 'action' => 'index']],
    ['title' => 'View', 'url' => null]
];
?>

<!-- Header Section -->
<?= $this->element('page_header', [
    'breadcrumbs' => $breadcrumbs
]) ?>


<div class="view-container">
    <!-- Header -->
    <div class="view-header">
        <div class="user-header">
            <div class="user-avatar"><?= $initials ?></div>
            <div class="user-info">
                <?php if ($patient->hasValue('user')): ?>
                    <h1><?= h($patient->user->first_name . ' ' . $patient->user->last_name) ?></h1>
                    <div class="user-meta">
                        <span class="user-meta-item">
                            <i class="bi bi-at"></i> <?= h($patient->user->username) ?>
                        </span>
                        <span class="badge-patient">Patient</span>
                        <span class="user-meta-item">
                            <i class="bi bi-hash"></i> ID: <?= $this->Number->format($patient->id) ?>
                        </span>
                    </div>
                <?php else: ?>
                    <h1>Patient #<?= h($patient->id) ?></h1>
                    <div class="user-meta">
                        <span class="badge-patient">Patient</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="view-actions">
            <?= $this->Html->link(
                '<i class="bi bi-pencil"></i> Edit',
                ['action' => 'edit', $patient->id],
                ['class' => 'btn btn-primary', 'escape' => false]
            ) ?>
<!--            --><?php //if (UserAccess::isAdmin($identity)): ?>
<!--                --><?php //= $this->Form->postLink(
//                    '<i class="bi bi-trash"></i> Delete',
//                    ['action' => 'delete', $patient->id],
//                    [
//                        'confirm' => __('Are you sure you want to delete patient #{0}?', $patient->id),
//                        'class' => 'btn btn-outline-danger',
//                        'escape' => false
//                    ]
//                ) ?>
<!--            --><?php //endif; ?>
        </div>
    </div>

    <!-- Patient Information -->
    <h2 class="section-title mb-3">
        <i class="bi bi-person-badge"></i> Patient Information
    </h2>
    <div class="detail-grid">
        <div class="detail-item user-patient">
            <div class="detail-label">
                <i class="bi bi-hash"></i> Patient ID
            </div>
            <div class="detail-value">
                #<?= $this->Number->format($patient->id) ?>
            </div>
        </div>
        <?php if ($patient->hasValue('user')): ?>
            <div class="detail-item user-patient">
                <div class="detail-label">
                    <i class="bi bi-person-circle"></i> Associated User
                </div>
                <div class="detail-value">
                    <?= $this->Html->link(
                        h($patient->user->first_name . ' ' . $patient->user->last_name) . ' (@' . h($patient->user->username) . ')',
                        ['controller' => 'Users', 'action' => 'view', $patient->user->id]
                    ) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Contact Information -->
    <?php if ($patient->hasValue('user')): ?>
        <h2 class="section-title mb-3 mt-4">
            <i class="bi bi-envelope"></i> Contact Information
        </h2>
        <div class="detail-grid">
            <div class="detail-item user-patient">
                <div class="detail-label">
                    <i class="bi bi-envelope"></i> Email
                </div>
                <div class="detail-value">
                    <a href="mailto:<?= h($patient->user->email) ?>" class="email-link">
                        <?= h($patient->user->email) ?>
                    </a>
                </div>
            </div>
            <div class="detail-item user-patient">
                <div class="detail-label">
                    <i class="bi bi-telephone"></i> Phone
                </div>
                <div class="detail-value <?= $patient->user->phone ? '' : 'text-muted' ?>">
                    <?= $patient->user->phone ? h($patient->user->phone) : 'Not provided' ?>
                </div>
            </div>
            <div class="detail-item user-patient">
                <div class="detail-label">
                    <i class="bi bi-geo-alt"></i> Address
                </div>
                <div class="detail-value <?= $patient->user->address ? '' : 'text-muted' ?>">
                    <?= $patient->user->address ? h($patient->user->address) : 'Not provided' ?>
                </div>
            </div>
            <div class="detail-item user-patient">
                <div class="detail-label">
                    <i class="bi bi-cake"></i> Date of Birth
                </div>
                <div class="detail-value <?= $patient->user->date_of_birth ? '' : 'text-muted' ?>">
                    <?= $patient->user->date_of_birth ? h($patient->user->date_of_birth->format('M d, Y')) : 'Not provided' ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Assigned Clinicians -->
    <?php if (!empty($patient->clinicians)): ?>
        <div class="related-section">
            <h2>
                <i class="bi bi-people"></i> Assigned Clinicians
            </h2>
            <div class="related-items">
                <?php foreach ($patient->clinicians as $clinician): ?>
                    <div class="related-item">
                        <div class="related-item-info">
                            <div>
                                <?php if ($clinician->hasValue('user')): ?>
                                    <div class="related-item-title">
                                        <?= h($clinician->user->first_name . ' ' . $clinician->user->last_name) ?>
                                    </div>
                                    <div class="related-item-subtitle">
                                        @<?= h($clinician->user->username) ?> • Clinician #<?= h($clinician->id) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="related-item-title">Clinician #<?= h($clinician->id) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="related-item-actions">
                            <?= $this->Html->link(
                                '<i class="bi bi-arrow-right-circle"></i> View Profile',
                                ['controller' => 'Clinicians', 'action' => 'view', $clinician->id],
                                ['class' => 'btn btn-sm btn-primary', 'escape' => false]
                            ) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Appointments -->
    <?php if (!empty($patient->appointments)): ?>
        <div class="related-section">
            <h2>
                <i class="bi bi-calendar-check"></i> Recent Appointments
            </h2>
            <div class="related-items">
                <?php foreach (array_slice($patient->appointments, 0, 5) as $appointment): ?>
                    <div class="related-item">
                        <div class="related-item-info">
                            <div>
                                <div class="related-item-title">
                                    <?= h($appointment->scheduled_at->format('M d, Y')) ?> at <?= h($appointment->scheduled_at->format('g:i A')) ?>
                                </div>
                                <div class="related-item-subtitle">
                                    Status: <span class="badge bg-<?= $appointment->status === 'completed' ? 'success' : 'primary' ?>">
                                        <?= h(ucfirst($appointment->status)) ?>
                                    </span>
                                    • Duration: <?= h($appointment->duration) ?> minutes
                                </div>
                            </div>
                        </div>
                        <div class="related-item-actions">
                            <?= $this->Html->link(
                                '<i class="bi bi-eye"></i> View',
                                ['controller' => 'Appointments', 'action' => 'view', $appointment->id],
                                ['class' => 'btn btn-sm btn-outline-primary', 'escape' => false]
                            ) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (count($patient->appointments) > 5): ?>
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Showing 5 of <?= count($patient->appointments) ?> appointments
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Vital Data Records -->
    <?php if (!empty($patient->vital_data)): ?>
        <div class="related-section">
            <h2>
                <i class="bi bi-heart-pulse"></i> Recent Health Records
            </h2>
            <div class="related-items">
                <?php foreach (array_slice($patient->vital_data, 0, 5) as $vitalData): ?>
                    <div class="related-item">
                        <div class="related-item-info">
                            <div>
                                <div class="related-item-title">
                                    <?= h($vitalData->recorded_at->format('M d, Y')) ?> at <?= h($vitalData->recorded_at->format('g:i A')) ?>
                                </div>
                                <div class="related-item-subtitle">
                                    O₂: <?= h($vitalData->oxygen_saturation) ?>%
                                    • RR: <?= h($vitalData->respiratory_rate) ?> bpm
                                    <?php if ($vitalData->blood_pressure): ?>
                                        • BP: <?= h($vitalData->blood_pressure) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="related-item-actions">
                            <?= $this->Html->link(
                                '<i class="bi bi-eye"></i> View',
                                ['controller' => 'VitalData', 'action' => 'view', $vitalData->id],
                                ['class' => 'btn btn-sm btn-outline-primary', 'escape' => false]
                            ) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (count($patient->vital_data) > 5): ?>
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Showing 5 of <?= count($patient->vital_data) ?> records
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
