<?php
$this->assign('title', 'Dashboard');

// CSS
$this->start('datatables_css');
echo $this->Html->css([
    'dashboard',
]);
$this->end();

// JS
$this->start('datatables_js');
echo $this->Html->script([]);
$this->end();

// Display the latest saved patient name
$displayName = trim(($currentUser->first_name ?? '') . ' ' . ($currentUser->last_name ?? ''));
?>

<!-- Header -->
<div class="dash-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <h1 class="dash-title mb-0">
        Welcome, <em><?= h($displayName !== '' ? $displayName : $currentUser->username) ?></em>
    </h1>

    <!-- Quick Actions -->
    <div class="d-flex gap-2 flex-wrap">
        <?= $this->Html->link(
            'Input Health Data',
            ['controller' => 'VitalData', 'action' => 'add'],
            ['class' => 'btn btn-primary'],
        ) ?>

        <?= $this->Html->link(
            'Contact Clinic',
            ['controller' => 'ContactMessages', 'action' => 'add'],
            ['class' => 'btn btn-primary'],
        ) ?>
    </div>
</div>

<div class="dashboard-sections">

    <!-- Latest Health Summary -->
    <div class="row g-3">
        <div class="col-12">
            <div class="stat-card">
                <div class="stat-label">Latest Health Summary</div>

                <?php if ($latestVital): ?>
                    <div class="mt-3 small">
                        <div><strong>Recorded:</strong> <?= h($latestVital->recorded_at?->format('d M Y, h:i A')) ?></div>
                        <div><strong>Oxygen Saturation:</strong> <?= h($latestVital->oxygen_saturation ?? '-') ?></div>
                        <div><strong>Respiratory Rate:</strong> <?= h($latestVital->respiratory_rate ?? '-') ?></div>
                        <div><strong>Weight:</strong> <?= h($latestVital->weight ?? '-') ?></div>
                        <div><strong>Symptoms:</strong>
                            <?php if (!empty($latestVital->symptoms)): ?>
                                <?= h(implode(', ', collection($latestVital->symptoms)->extract('symptom_name')->toList())) ?>
                            <?php else: ?>
                                None recorded
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-muted mt-3">No health data has been submitted yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Appointment and Resource Preview -->
    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="stat-card">
                <div class="stat-label">Next Appointment</div>

                <?php if ($nextAppointment): ?>
                    <div class="mt-3">
                        <div class="fs-5"><?= h($nextAppointment->scheduled_at?->format('d M Y, h:i A')) ?></div>
                        <div class="text-muted small mt-1">Status: <?= h($nextAppointment->status) ?></div>
                    </div>
                <?php else: ?>
                    <div class="text-muted mt-3">No appointments scheduled.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="stat-card">
                <div class="stat-label">Latest Resource</div>

                <?php if ($latestResource): ?>
                    <div class="mt-3">
                        <div class="fw-semibold"><?= h($latestResource->title) ?></div>
                        <div class="text-muted small mt-1"><?= h($latestResource->type) ?></div>

                        <?php if (!empty($latestResource->body)): ?>
                            <div class="small mt-2">
                                <?= h(mb_strimwidth($latestResource->body, 0, 120, '...')) ?>
                            </div>
                        <?php endif; ?>

                        <div class="mt-3">
                            <?= $this->Html->link(
                                'View All Resources',
                                ['controller' => 'Resources', 'action' => 'index'],
                                ['class' => 'btn btn-outline-primary btn-sm'],
                            ) ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-muted mt-3">No resources are available right now.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


