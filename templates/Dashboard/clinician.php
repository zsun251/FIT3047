<?= $this->Html->css('clincian-dashboard.css') ?>

<div class="container-fluid clinician-dashboard px-4">

    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="welcome-left">
            <img src="<?= $this->Url->image('doctor-illustration.png') ?>" alt="Doctor">

            <div class="welcome-text">
                <h1>
                    Welcome back,
                    <span><?= h($user->first_name ?? 'Dr John') ?></span> 👋
                </h1>

                <p>
                    Here's an overview of your patient activity
                    and important updates.
                </p>
            </div>
        </div>

        <div class="welcome-actions">

            <a href="<?= $this->Url->build(['controller' => 'Patients', 'action' => 'index']) ?>"
               class="action-card primary">

                <i class="bi bi-people-fill action-icon"></i>

                <span>View Patients</span>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Appointments', 'action' => 'index']) ?>"
               class="action-card secondary">

                <i class="bi bi-calendar-check action-icon"></i>

                <span>Appointments</span>
            </a>

        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">

        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="bi bi-people"></i>
            </div>

            <div>
                <h6>Assigned Patients</h6>
                <h2><?= $stats['patients'] ?? 12 ?></h2>
                <p>Currently assigned</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="bi bi-calendar-week"></i>
            </div>

            <div>
                <h6>Today's Appointments</h6>
                <h2><?= $stats['appointments'] ?? 3 ?></h2>
                <p class="blue-text">Scheduled today</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <i class="bi bi-clipboard2-pulse"></i>
            </div>

            <div>
                <h6>Total Health Records</h6>
                <h2><?= $stats['records'] ?? 84 ?></h2>
                <p>Submitted records</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon red">
                <i class="bi bi-exclamation-triangle"></i>
            </div>

            <div>
                <h6>Critical Alerts</h6>
                <h2><?= $stats['alerts'] ?? 1 ?></h2>
                <p class="red-text">Abnormal readings</p>
            </div>
        </div>

    </div>

    <!-- Bottom Grid -->
    <div class="bottom-grid">

        <!-- Recent Activity -->
        <div class="dashboard-card">
            <div class="card-header-custom">
                <h5>Recent Patient Activity</h5>
            </div>

            <div class="activity-list">

                <?php if (!$recentVitals->isEmpty()): ?>

                    <?php foreach ($recentVitals as $vital): ?>

                        <div class="activity-item">

                            <div class="activity-icon blue">
                                <i class="bi bi-activity"></i>
                            </div>

                            <div class="activity-content">
                                <strong>
                                    <?= h($vital->patient->user->first_name ?? 'Patient') ?>
                                    <?= h($vital->patient->user->last_name ?? '') ?>
                                </strong>

                                <span>Submitted new vital signs</span>
                            </div>

                            <small>
                                <?= h($vital->recorded_at->timeAgoInWords()) ?>
                            </small>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="empty-activity">
                        No recent patient activity available.
                    </div>

                <?php endif; ?>

            </div>
        </div>

        <!-- Appointments -->
        <div class="dashboard-card">
            <div class="card-header-custom">
                <h5>Upcoming Appointments</h5>

                <a href="<?= $this->Url->build([
                    'controller' => 'Appointments',
                    'action' => 'index'
                ]) ?>" class="view-all-link">

                    View all
                    <i class="bi bi-arrow-right-short"></i>

                </a>
            </div>

            <div class="appointment-list">

                <?php if (!$appointments->isEmpty()): ?>

                    <?php foreach ($appointments as $appointment): ?>

                        <div class="appointment-item">

                            <div class="appointment-time">
                                <?= h($appointment->scheduled_at->format('h:i A')) ?>
                            </div>

                            <div>

                                <strong>
                                    <?= h($appointment->patient->user->first_name ?? 'Patient') ?>
                                    <?= h($appointment->patient->user->last_name ?? '') ?>
                                </strong>

                                <p>
                                    <?= h($appointment->appointment_type ?? 'Appointment') ?>
                                </p>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="empty-appointments">
                        No upcoming appointments.
                    </div>

                <?php endif; ?>

            </div>
        </div>

    </div>

</div>
