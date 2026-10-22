<?php
/**
 * Reminders Dropdown Element
 *
 * Displays a notification dropdown with patient's active reminders
 *
 * @var \App\View\AppView $this
 */
use App\Policy\UserAccess;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;

// Get reminders data server-side
$remindersTable = TableRegistry::getTableLocator()->get('Reminders');
$identity = $this->request->getAttribute('identity');
$patientId = UserAccess::getPatientId($identity);

// Check if reminders are enabled
$reminderSettingsTable = TableRegistry::getTableLocator()->get('ReminderSettings');
$settings = $reminderSettingsTable->getPatientSettings($patientId);

// Only show reminders if active
if ($settings && $settings->is_active) {
    // Get all active reminders
    $allActiveReminders = $remindersTable->getActiveReminders($patientId);

    // Filter to only today's reminders
    $now = FrozenTime::now();
    $todayReminders = array_filter($allActiveReminders, function($reminder) use ($now) {
        return $reminder->scheduled_at->format('Y-m-d') === $now->format('Y-m-d');
    });

    // Count today's reminders
    $reminderCount = count($todayReminders);

    // Get recent 5 for dropdown (sorted ascending by scheduled time)
    usort($todayReminders, function($a, $b) {
        return $a->scheduled_at <=> $b->scheduled_at;
    });
    $recentReminders = array_slice($todayReminders, 0, 5);
} else {
    $reminderCount = 0;
    $recentReminders = [];
}
?>

<?php if ($settings && $settings->is_active): ?>
    <li class="nav-item dropdown topbar-always-show" id="remindersDropdownItem">
        <a class="nav-link position-relative"
           href="#"
           id="remindersDropdown"
           role="button"
           data-bs-toggle="dropdown"
           aria-expanded="false">
            <i class="bi bi-bell-fill"></i>
            <?php if ($reminderCount > 0): ?>
                <span class="badge bg-danger rounded-pill position-absolute reminder-badge pulse">
                    <?= $reminderCount ?>
                </span>
            <?php endif; ?>
        </a>

        <ul class="dropdown-menu dropdown-menu-end reminders-dropdown"
            aria-labelledby="remindersDropdown">
            <li class="dropdown-header d-flex justify-content-between align-items-center">
                <span><strong>Active Reminders</strong></span>
                <span class="badge bg-primary"><?= $reminderCount ?></span>
            </li>

            <li><hr class="dropdown-divider"></li>

            <li class="reminder-items-container">
                <?php if (!empty($recentReminders)): ?>
                    <?php foreach ($recentReminders as $reminder): ?>
                        <a href="<?= $this->Url->build(['controller' => 'Reminders', 'action' => 'index']) ?>"
                           class="reminder-item">
                            <div class="reminder-item-message">
                                <?= h($reminder->message) ?>
                            </div>
                            <div class="reminder-item-time">
                                <i class="bi bi-clock"></i>
                                <span>Scheduled: <?= $reminder->scheduled_at->format('g:i A') ?></span>
                                <?php
                                $now = FrozenTime::now();
                                if ($reminder->scheduled_at <= $now):
                                    ?>
                                    <span class="reminder-item-overdue">• Due now</span>
                                <?php endif; ?>
                            </div>
                            <div class="reminder-item-time">
                                <small class="text-muted">
                                    <?php
                                    if ($reminder->scheduled_at > $now) {
                                        // Upcoming reminder
                                        $diff = $reminder->scheduled_at->diff($now);
                                        if ($diff->h > 0) {
                                            echo 'In ' . $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
                                            if ($diff->i > 0) {
                                                echo ' ' . $diff->i . ' min';
                                            }
                                        } elseif ($diff->i > 0) {
                                            echo 'In ' . $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
                                        } else {
                                            echo 'Starting soon';
                                        }
                                    } else {
                                        // Overdue reminder
                                        $diff = $now->diff($reminder->scheduled_at);
                                        if ($diff->h > 0) {
                                            echo $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
                                        } elseif ($diff->i > 0) {
                                            echo $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
                                        } else {
                                            echo 'Just now';
                                        }
                                    }
                                    ?>
                                </small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="reminder-empty">
                        <i class="bi bi-check-circle"></i>
                        <div>No active reminders</div>
                        <small>You're all caught up!</small>
                    </div>
                <?php endif; ?>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <?= $this->Html->link(
                    '<i class="bi bi-list-ul"></i> View All Reminders',
                    ['controller' => 'Reminders', 'action' => 'index'],
                    [
                        'class' => 'dropdown-item text-center fw-bold',
                        'escape' => false
                    ]
                ) ?>
            </li>
        </ul>
    </li>
<?php endif; ?>
