<?php
declare(strict_types=1);

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $currentUser
 * @var mixed $latestVital
 * @var mixed $nextAppointment
 * @var mixed $latestResource
 */

use App\Policy\UserAccess;
use Cake\ORM\TableRegistry;

$this->assign('title', 'Dashboard');

// CSS
$this->start('datatables_css');
echo $this->Html->css([
    'form-shared',
    'dashboard-patient',
]);
$this->end();

// JS
$this->start('datatables_js');
echo $this->Html->script([]);
$this->end();

$patientName = trim(
    (string)($currentUser->first_name ?? '') . ' ' . (string)($currentUser->last_name ?? '')
);

if ($patientName === '') {
    $patientName = (string)($currentUser->username ?? 'Patient');
}

$identity = $this->request->getAttribute('identity');
$isStaff = UserAccess::isStaff($identity);

$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => null]
];

// Check if reminders are enabled
$identity = $this->request->getAttribute('identity');
$patientId = UserAccess::getPatientId($identity);
$reminderSettingsTable = TableRegistry::getTableLocator()->get('ReminderSettings');
$settings = $reminderSettingsTable->getPatientSettings($patientId);
?>

<!-- Header Section -->
<?= $this->element('page_header', [
    'breadcrumbs' => $breadcrumbs
]) ?>

<div class="form-container patient-dashboard">
    <div class="form-header patient-dashboard-header">
        <div class="patient-dashboard-heading">
            <h1>
                <?= __('Welcome, ') ?>
                <span class="patient-dashboard-name"><?= h($patientName) ?></span>
            </h1>        </div>

        <div class="patient-dashboard-actions">
            <?= $this->Html->link(
                '<i class="bi bi-heart-pulse"></i><span>Input Health Data</span>',
                ['controller' => 'VitalData', 'action' => 'add'],
                ['class' => 'btn btn-primary', 'escape' => false],
            ) ?>

            <?= $this->Html->link(
                '<i class="bi bi-envelope"></i><span>Contact Clinic</span>',
                ['controller' => 'ContactMessages', 'action' => 'add'],
                ['class' => 'btn btn-outline-primary', 'escape' => false],
            ) ?>
        </div>
    </div>

    <div class="form-info-alert">
        <i class="bi bi-info-circle"></i>
        <p>
            <?= $this->ContentBlock->text('dashboard-patient-help-text') ?>
        </p>
    </div>

    <!-- Active Reminders Section -->
    <?php if ($settings && $settings->is_active): ?>
        <div class="form-section patient-dashboard-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="form-section-title mb-0">
                    <i class="bi bi-bell"></i>
                    <?= __('Active Reminders') ?>
                    <?php if ($reminderCount > 0): ?>
                        <span class="badge bg-danger ms-2"><?= $reminderCount ?></span>
                    <?php endif; ?>
                </h2>
                <?= $this->Html->link(
                    __('View All'),
                    ['controller' => 'Reminders', 'action' => 'index'],
                    ['class' => 'btn btn-sm btn-outline-primary']
                ) ?>
            </div>

            <?php if ($reminderCount > 0 && !empty($activeReminders)): ?>
                <div class="reminders-list-compact">
                    <?php foreach ($activeReminders as $reminder): ?>
                        <div class="reminder-card-compact">
                            <div class="d-flex align-items-start gap-3">
                                <div class="reminder-icon-compact">
                                    <i class="bi bi-bell-fill text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="reminder-message-compact">
                                        <?= h($reminder->message) ?>
                                    </div>
                                    <div class="reminder-time-compact">
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i>
                                            <?= $reminder->scheduled_at->format('g:i A') ?>
                                        </small>
                                        <?php if ($reminder->isOverdue()): ?>
                                            <small class="text-danger ms-2">
                                                <i class="bi bi-exclamation-circle"></i>
                                                <strong>Log Health</strong>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <?= $this->Html->link(
                                        '<i class="bi bi-plus-circle"></i>',
                                        ['controller' => 'VitalData', 'action' => 'add', '?' => ['reminder_id' => $reminder->id]],
                                        [
                                            'class' => 'btn btn-sm btn-primary',
                                            'escape' => false,
                                            'title' => 'Log Vitals'
                                        ]
                                    ) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="patient-empty-state">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <p><?= __('No active reminders') ?></p>
                    <small class="text-muted"><?= __("You're all caught up!") ?></small>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="form-section patient-dashboard-section">
        <h2 class="form-section-title">
            <i class="bi bi-activity"></i>
            <?= __('Latest Health Summary') ?>
        </h2>

        <?php if ($latestVital): ?>
            <div class="patient-summary-grid">
                <div class="patient-summary-item">
                    <div class="patient-summary-label"><?= __('Recorded') ?></div>
                    <div class="patient-summary-value">
                        <?= h($latestVital->recorded_at?->format('d M Y, h:i A')) ?>
                    </div>
                </div>

                <div class="patient-summary-item">
                    <div class="patient-summary-label"><?= __('Oxygen Saturation') ?></div>
                    <div class="patient-summary-value">
                        <?= h($latestVital->oxygen_saturation ?? '-') ?>
                    </div>
                </div>

                <div class="patient-summary-item">
                    <div class="patient-summary-label"><?= __('Respiratory Rate') ?></div>
                    <div class="patient-summary-value">
                        <?= h($latestVital->respiratory_rate ?? '-') ?>
                    </div>
                </div>

                <div class="patient-summary-item">
                    <div class="patient-summary-label"><?= __('Weight') ?></div>
                    <div class="patient-summary-value">
                        <?= h($latestVital->weight ?? '-') ?>
                    </div>
                </div>

                <div class="patient-summary-item patient-summary-item-full">
                    <div class="patient-summary-label"><?= __('Symptoms') ?></div>
                    <div class="patient-summary-value">
                        <?php if (!empty($latestVital->symptoms)): ?>
                            <?= h(implode(', ', collection($latestVital->symptoms)->extract('symptom_name')->toList())) ?>
                        <?php else: ?>
                            <?= __('None recorded') ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="patient-empty-state">
                <?= __('No health data has been submitted yet.') ?>
            </div>
        <?php endif; ?>
    </div>



    <div class="patient-dashboard-grid">
        <div class="form-section patient-dashboard-section">
            <h2 class="form-section-title">
                <i class="bi bi-calendar-event"></i>
                <?= __('Next Appointment') ?>
            </h2>

            <?php if ($nextAppointment): ?>
                <div class="patient-preview-card">
                    <div class="patient-preview-title">
                        <?= h($nextAppointment->scheduled_at?->format('d M Y, h:i A')) ?>
                    </div>
                    <div class="patient-preview-meta">
                        <?= __('Status: {0}', h($nextAppointment->status)) ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="patient-empty-state">
                    <?= __('No appointments scheduled.') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-section patient-dashboard-section">
            <h2 class="form-section-title">
                <i class="bi bi-journal-text"></i>
                <?= __('Latest Resource') ?>
            </h2>

            <?php if ($latestResource): ?>
                <div class="patient-preview-card">
                    <div class="patient-preview-title">
                        <?= h($latestResource->title) ?>
                    </div>

                    <?php if (!empty($latestResource->type)): ?>
                        <div class="patient-preview-meta">
                            <?= h($latestResource->type) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($latestResource->body)): ?>
                        <div class="patient-preview-text">
                            <?= h(mb_strimwidth($latestResource->body, 0, 160, '...')) ?>
                        </div>
                    <?php endif; ?>

                    <div class="patient-preview-actions">
                        <?= $this->Html->link(
                            __('View All Resources'),
                            ['controller' => 'Resources', 'action' => 'browse'],
                            ['class' => 'btn btn-outline-primary btn-sm'],
                        ) ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="patient-empty-state">
                    <?= __('No resources are available right now.') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
