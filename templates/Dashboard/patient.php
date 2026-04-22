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
            <?= __('Use your dashboard to quickly review your latest health information, check upcoming appointments, and access helpful resources.') ?>
        </p>
    </div>

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
