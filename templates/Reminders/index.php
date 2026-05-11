<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Reminder[] $activeReminders
 * @var \App\Model\Entity\Reminder[] $completedReminders
 * @var \App\Model\Entity\Reminder[] $dismissedReminders
 * @var \App\Model\Entity\VitalsReminder|null $config
 */
$this->assign('title', 'My Reminders');

// CSS
$this->Html->css('reminders-index', ['block' => true]);

// JS
$this->Html->script('reminders-index', ['block' => true]);
?>

    <!-- Header Section -->
    <div class="dash-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="dash-title mb-1">My Reminders</h1>
            <p class="text-muted mb-0">Manage your vital data entry reminders</p>
        </div>
        <div>
            <?= $this->Html->link(
                '<i class="bi bi-gear"></i> Settings',
                ['action' => 'settings'],
                [
                    'class' => 'btn btn-outline-primary',
                    'escape' => false
                ]
            ) ?>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-6">
            <div class="stat-card">
                <div class="stat-label">Active Reminders</div>
                <div class="stat-value text-primary"><?= count($activeReminders) ?></div>
            </div>
        </div>
        <div class="col-6 col-md-6">
            <div class="stat-card">
                <div class="stat-label">Completed Today</div>
                <div class="stat-value text-success"><?= count(array_filter($completedReminders, function($r) {
                        return $r->interacted_at && $r->interacted_at->isToday();
                    })) ?></div>
            </div>
        </div>
    </div>

    <!-- Active Reminders Section -->
    <div class="section-card mb-4">
        <div class="section-header">
            <h3><i class="bi bi-bell"></i> Active Reminders</h3>
        </div>

        <?php if (empty($activeReminders)): ?>
            <div class="empty-state">
                <i class="bi bi-bell-slash"></i>
                <p>No active reminders</p>
                <small class="text-muted">You're all caught up!</small>
            </div>
        <?php else: ?>
            <div class="reminders-list">
                <?php foreach ($activeReminders as $reminder): ?>
                    <div class="reminder-card active">
                        <div class="reminder-icon">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="reminder-content">
                            <div class="reminder-message"><?= h($reminder->message) ?></div>
                            <div class="reminder-time">
                                <small class="text-muted">
                                    <i class="bi bi-calendar3"></i>
                                    <?= $reminder->scheduled_at->format('M d, Y g:i A') ?>
                                </small>
                            </div>
                        </div>
                        <div class="reminder-actions">
                            <?= $this->Html->link(
                                '<i class="bi bi-plus-circle"></i> Log Vitals',
                                ['controller' => 'VitalData', 'action' => 'add'],
                                ['class' => 'btn btn-sm btn-primary', 'escape' => false]
                            ) ?>
                            <?= $this->Form->postLink(
                                '<i class="bi bi-x-circle"></i> Dismiss',
                                ['action' => 'dismiss', $reminder->id],
                                [
                                    'class' => 'btn btn-sm btn-outline-secondary',
                                    'escape' => false,
                                    'confirm' => 'Dismiss this reminder?'
                                ]
                            ) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Completed Reminders Section -->
    <div class="section-card mb-4">
        <div class="section-header">
            <h3><i class="bi bi-check-circle"></i> Completed Today</h3>
        </div>

        <?php
        $todayCompleted = array_filter($completedReminders, function($r) {
            return $r->interacted_at && $r->interacted_at->isToday();
        });
        ?>

        <?php if (empty($todayCompleted)): ?>
            <div class="empty-state">
                <i class="bi bi-clipboard-check"></i>
                <p>No completed reminders today</p>
            </div>
        <?php else: ?>
            <div class="reminders-list">
                <?php foreach ($todayCompleted as $reminder): ?>
                    <div class="reminder-card completed">
                        <div class="reminder-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div class="reminder-content">
                            <div class="reminder-message"><?= h($reminder->message) ?></div>
                            <div class="reminder-time">
                                <small class="text-muted">
                                    Completed at <?= $reminder->interacted_at->format('g:i A') ?>
                                </small>
                            </div>
                            <?php if ($reminder->vital_data): ?>
                                <div class="mt-1">
                                    <?= $this->Html->link(
                                        '<i class="bi bi-eye"></i> View Entry',
                                        ['controller' => 'VitalData', 'action' => 'view', $reminder->vital_data->id],
                                        ['class' => 'btn btn-xs btn-outline-primary', 'escape' => false]
                                    ) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Reminder Schedule Info -->
<?php if ($config): ?>
    <div class="section-card">
        <div class="section-header">
            <h3><i class="bi bi-calendar-week"></i> Your Schedule</h3>
        </div>
        <div class="schedule-info">
            <p class="mb-3">You will receive reminders at:</p>
            <div class="row g-2">
                <?php if ($config->time_slot_1): ?>
                    <div class="col-6 col-md-3">
                        <div class="time-slot-badge">
                            <i class="bi bi-clock"></i> <?= date('g:i A', strtotime($config->time_slot_1)) ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($config->time_slot_2): ?>
                    <div class="col-6 col-md-3">
                        <div class="time-slot-badge">
                            <i class="bi bi-clock"></i> <?= date('g:i A', strtotime($config->time_slot_2)) ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($config->time_slot_3): ?>
                    <div class="col-6 col-md-3">
                        <div class="time-slot-badge">
                            <i class="bi bi-clock"></i> <?= date('g:i A', strtotime($config->time_slot_3)) ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($config->time_slot_4): ?>
                    <div class="col-6 col-md-3">
                        <div class="time-slot-badge">
                            <i class="bi bi-clock"></i> <?= date('g:i A', strtotime($config->time_slot_4)) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mt-3">
                <small class="text-muted">
                    <i class="bi bi-envelope"></i>
                    Email notifications: <?= $config->email_enabled ? '<span class="text-success">Enabled</span>' : '<span class="text-muted">Disabled</span>' ?>
                </small>
            </div>
        </div>
    </div>
<?php endif; ?>
