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
    <div class="patient-hero">
        <div>
            <p class="patient-hero-label">Patient Dashboard</p>

            <h1>
                Welcome back,
                <span class="patient-dashboard-name"><?= h($patientName) ?></span>
            </h1>

            <p class="patient-hero-text">
                Here’s a quick overview of your latest health updates, appointments, and resources.
            </p>
        </div>

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
            <div class="patient-stats-grid">

                <div class="patient-stat-card">
                    <div class="patient-stat-icon purple">
                        <i class="bi bi-droplet-half"></i>
                    </div>

                    <div class="patient-stat-content">
                        <div class="patient-stat-label">Oxygen Saturation</div>

                        <div class="patient-stat-value">
                            <?= h($latestVital->oxygen_saturation ?? '-') ?>%
                        </div>

                        <span class="patient-stat-badge">
                    Latest Reading
                </span>
                    </div>
                </div>

                <div class="patient-stat-card">
                    <div class="patient-stat-icon green">
                        <i class="bi bi-wind"></i>
                    </div>

                    <div class="patient-stat-content">
                        <div class="patient-stat-label">Respiratory Rate</div>

                        <div class="patient-stat-value">
                            <?= h($latestVital->respiratory_rate ?? '-') ?>
                            <span class="patient-stat-unit">breaths/min</span>
                        </div>

                        <span class="patient-stat-badge">
                    Latest Reading
                </span>
                    </div>
                </div>

                <div class="patient-stat-card">
                    <div class="patient-stat-icon blue">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>

                    <div class="patient-stat-content">
                        <div class="patient-stat-label">Weight</div>

                        <div class="patient-stat-value">
                            <?= h($latestVital->weight ?? '-') ?> kg
                        </div>

                        <span class="patient-stat-badge">
                    Latest Reading
                </span>
                    </div>
                </div>

                <div class="patient-stat-card">
                    <div class="patient-stat-icon pink">
                        <i class="bi bi-heart-pulse"></i>
                    </div>

                    <div class="patient-stat-content">
                        <div class="patient-stat-label">Blood Pressure</div>

                        <div class="patient-stat-value">
                            <?= h($latestVital->blood_pressure ?? '-') ?>
                            <span class="patient-stat-unit">mmHg</span>
                        </div>

                        <span class="patient-stat-badge">
                    Latest Reading
                </span>
                    </div>
                </div>

                <div class="patient-stat-card">
                    <div class="patient-stat-icon purple">
                        <i class="bi bi-activity"></i>
                    </div>

                    <div class="patient-stat-content">
                        <div class="patient-stat-label">Pulse Oximetry</div>

                        <div class="patient-stat-value">
                            <?= h($latestVital->pulse_oximetry ?? '-') ?>%
                        </div>

                        <span class="patient-stat-badge">
                    Latest Reading
                </span>
                    </div>
                </div>

            </div>

            <div class="patient-symptoms-card">
                <div class="patient-symptoms-icon">
                    <i class="bi bi-chat-dots-fill"></i>
                </div>

                <div class="patient-symptoms-content">
                    <h2>Symptoms</h2>

                    <p>
                        <?php if (!empty($latestVital->symptoms)): ?>
                            <?= h(implode(', ', collection($latestVital->symptoms)->extract('symptom_name')->toList())) ?>
                        <?php else: ?>
                            None recorded
                        <?php endif; ?>
                    </p>
                </div>

                <?= $this->Html->link(
                    'View History <i class="bi bi-chevron-right"></i>',
                    ['controller' => 'VitalData', 'action' => 'patientHistory'],
                    ['class' => 'patient-history-btn', 'escape' => false]
                ) ?>
            </div>

        <?php else: ?>
            <div class="patient-empty-state">
                <?= __('No health data has been submitted yet.') ?>
            </div>
        <?php endif; ?>
    </div>



    <div class="patient-dashboard-grid">

        <!-- Upcoming Appointments Card -->
        <div class="patient-preview-card">
            <div class="patient-preview-header">
                <div class="patient-preview-title-group">
                    <i class="bi bi-calendar-event"></i>
                    <h3>Upcoming Appointments</h3>
                </div>

                <?= $this->Html->link(
                    'View History <i class="bi bi-chevron-right"></i>',
                    ['controller' => 'Appointments', 'action' => 'upcomingAppointments'],
                    ['class' => 'patient-history-btn', 'escape' => false]
                ) ?>
            </div>

            <?php if (!empty($upcomingAppointments) && $upcomingAppointments->count() > 0): ?>
                <?php foreach ($upcomingAppointments as $appointment): ?>
                    <div class="patient-preview-box appointment-box empty-appointment">
                        <div class="patient-preview-icon">
                            <i class="bi bi-calendar2-week"></i>
                        </div>

                        <div class="patient-preview-content">
                            <div class="patient-preview-main">
                                <?= h($appointment->scheduled_at->format('d M Y, g:i A')) ?>
                            </div>

                            <div class="patient-preview-sub">
                                <?= h(trim(($appointment->clinician->user->first_name ?? '') . ' ' . ($appointment->clinician->user->last_name ?? ''))) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="patient-preview-box appointment-box empty-appointment">
                    <div class="patient-preview-icon">
                        <i class="bi bi-calendar2-week"></i>
                    </div>

                    <div class="patient-preview-content">
                        <div class="patient-preview-main">No appointments scheduled.</div>
                        <div class="patient-preview-sub">You're all caught up!</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Past Appointments Card -->
        <div class="patient-preview-card">
            <div class="patient-preview-header">
                <div class="patient-preview-title-group">
                    <i class="bi bi-clock-history"></i>
                    <h3>Past Appointments</h3>
                </div>

                <?= $this->Html->link(
                    'View History <i class="bi bi-chevron-right"></i>',
                    ['controller' => 'Appointments', 'action' => 'upcomingAppointments'],
                    ['class' => 'patient-history-btn', 'escape' => false]
                ) ?>
            </div>

            <?php if (!empty($pastAppointments) && $pastAppointments->count() > 0): ?>
                <?php foreach ($pastAppointments as $appointment): ?>
                    <div class="patient-preview-box appointment-box">
                        <div class="patient-preview-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>

                        <div class="patient-preview-content">
                            <div class="patient-preview-main">
                                <?= h($appointment->scheduled_at->format('d M Y, g:i A')) ?>
                            </div>

                            <div class="patient-preview-sub">
                                <?= h(trim(($appointment->clinician->user->first_name ?? '') . ' ' . ($appointment->clinician->user->last_name ?? ''))) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="patient-preview-box appointment-box">
                    <div class="patient-preview-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>

                    <div class="patient-preview-content">
                        <div class="patient-preview-main">No previous appointments.</div>
                        <div class="patient-preview-sub">Past appointments will appear here.</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
