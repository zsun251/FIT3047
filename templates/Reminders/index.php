<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Reminder[] $activeReminders
 * @var \App\Model\Entity\Reminder[] $upcomingReminders
 * @var \App\Model\Entity\Reminder[] $completedReminders
 * @var \App\Model\Entity\Reminder[] $dismissedReminders
 * @var \App\Model\Entity\ReminderSetting|null $settings
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
                <div class="stat-value text-primary"><?= count($activeReminders) + count($upcomingReminders) ?></div>
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

    <!-- Upcoming Reminders Section -->
    <?php if (!empty($upcomingReminders)): ?>
        <div class="section-card mb-4">
            <div class="section-header">
                <h3><i class="bi bi-clock-history"></i> Upcoming Today</h3>
                <small class="text-muted">Reminders scheduled for later today</small>
            </div>

            <div class="reminders-list">
                <?php foreach ($upcomingReminders as $reminder): ?>
                    <div class="reminder-card upcoming">
                        <div class="reminder-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="reminder-content">
                            <div class="reminder-message"><?= h($reminder->message) ?></div>
                            <div class="reminder-time">
                                <small class="text-muted">
                                    <i class="bi bi-calendar3"></i>
                                    Scheduled: <?= $reminder->scheduled_at->format('g:i A') ?>
                                </small>
                                <br>
                                <small class="text-info">
                                    <i class="bi bi-info-circle"></i>
                                    <?php if ($reminder->hours_until > 0): ?>
                                        <strong>In <?= $reminder->hours_until ?> hour<?= $reminder->hours_until != 1 ? 's' : '' ?>
                                            <?php if ($reminder->remaining_minutes > 0): ?>
                                                <?= $reminder->remaining_minutes ?> min<?= $reminder->remaining_minutes != 1 ? 's' : '' ?>
                                            <?php endif; ?>
                                        </strong>
                                    <?php else: ?>
                                        <strong>In <?= $reminder->minutes_until ?> minute<?= $reminder->minutes_until != 1 ? 's' : '' ?></strong>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                        <div class="reminder-actions">
                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                <i class="bi bi-clock"></i> Not Yet
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

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
                    <div class="reminder-card active" data-scheduled-time="<?= $reminder->scheduled_at->toIso8601String() ?>">
                        <div class="reminder-icon">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="reminder-content">
                            <div class="reminder-message"><?= h($reminder->message) ?></div>
                            <div class="reminder-time">
                                <small class="text-muted">
                                    <i class="bi bi-calendar3"></i>
                                    Scheduled: <?= $reminder->scheduled_at->format('M d, Y g:i A') ?>
                                </small>
                                <?php if ($reminder->isOverdue()): ?>
                                    <br>
                                    <small class="text-danger">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <strong>Overdue</strong>
                                    </small>
                                <?php endif; ?>
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
            return $r->interacted_at && $r->interacted_at->format('Y-m-d') === date('Y-m-d');
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
<?php if ($settings && $settings->is_active): ?>
    <div class="section-card">
        <div class="section-header">
            <h3><i class="bi bi-calendar-week"></i> Your Schedule</h3>
        </div>
        <div class="schedule-info">
            <p class="mb-3">You will receive reminders at:</p>
            <div class="row g-2">
                <?php if ($settings->time_slot_1): ?>
                    <div class="col-6 col-md-3">
                        <div class="time-slot-badge">
                            <i class="bi bi-clock"></i> <?= $settings->time_slot_1->format('g:i A') ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($settings->time_slot_2): ?>
                    <div class="col-6 col-md-3">
                        <div class="time-slot-badge">
                            <i class="bi bi-clock"></i> <?= $settings->time_slot_2->format('g:i A') ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($settings->time_slot_3): ?>
                    <div class="col-6 col-md-3">
                        <div class="time-slot-badge">
                            <i class="bi bi-clock"></i> <?= $settings->time_slot_3->format('g:i A') ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($settings->time_slot_4): ?>
                    <div class="col-6 col-md-3">
                        <div class="time-slot-badge">
                            <i class="bi bi-clock"></i> <?= $settings->time_slot_4->format('g:i A') ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mt-3">
                <small class="text-muted">
                    <i class="bi bi-envelope"></i>
                    Email notifications: <?= $settings->email_enabled ? '<span class="text-success">Enabled</span>' : '<span class="text-muted">Disabled</span>' ?>
                </small>
                <?php if ($settings->sms_enabled): ?>
                    <br>
                    <small class="text-muted">
                        <i class="bi bi-phone"></i>
                        SMS notifications: <span class="text-success">Enabled</span>
                    </small>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php elseif ($settings && !$settings->is_active): ?>
    <div class="section-card">
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i>
            Reminders are currently disabled.
            <?= $this->Html->link('Enable them in settings', ['action' => 'settings'], ['class' => 'alert-link']) ?>
        </div>
    </div>
<?php endif; ?>
