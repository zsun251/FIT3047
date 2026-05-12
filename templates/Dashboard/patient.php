<?php
declare(strict_types=1);

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $currentUser
 * @var mixed $latestVital
 * @var mixed $nextAppointment
 * @var mixed $latestResource
 */

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
?>

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
            <?= __('Use your dashboard to quickly review your latest health information, check upcoming appointments, and access helpful resources.') ?>
        </p>
    </div>

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

        <div class="patient-preview-card">
            <div class="patient-preview-header">
                <div class="patient-preview-title-group">
                    <i class="bi bi-calendar-event"></i>
                    <h3>Next Appointment</h3>
                </div>
            </div>

            <div class="patient-preview-box appointment-box">
                <div class="patient-preview-icon">
                    <i class="bi bi-calendar2-week"></i>
                </div>

                <div class="patient-preview-content">
                    <div class="patient-preview-main">
                        No appointments scheduled.
                    </div>

                    <div class="patient-preview-sub">
                        You're all caught up!
                    </div>
                </div>
            </div>
        </div>

        <div class="patient-preview-card">
            <div class="patient-preview-header">
                <div class="patient-preview-title-group">
                    <i class="bi bi-journal-text"></i>
                    <h3>Latest Resource</h3>
                </div>
            </div>

            <div class="patient-preview-box resource-box">
                <div class="patient-preview-icon">
                    <i class="bi bi-journal-medical"></i>
                </div>

                <div class="patient-preview-content">
                    <div class="patient-preview-main">
                        No resources available right now.
                    </div>

                    <div class="patient-preview-sub">
                        Check back later for updates.
                    </div>
                </div>
            </div>
        </div>

    </div>
