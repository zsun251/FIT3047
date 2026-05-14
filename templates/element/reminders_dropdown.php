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

// Get pending count
$reminderCount = $remindersTable->getPendingCount($patientId);

// Get recent active reminders for dropdown
$now = FrozenTime::now();
$recentReminders = $remindersTable->find()
    ->innerJoinWith('VitalsReminders')
    ->where([
        'VitalsReminders.patient_id' => $patientId,
        'Reminders.scheduled_at <=' => $now,
        'Reminders.interaction_type IS' => null
    ])
    ->orderBy(['Reminders.scheduled_at' => 'DESC'])
    ->limit(5)
    ->all();
?>

<!-- Rest of dropdown HTML remains the same -->

<?php if ($settings && $settings->is_active): ?>
    <li class="nav-item dropdown topbar-always-show" id="remindersDropdownItem">
        <a class="nav-link position-relative"
           href="#"
           id="remindersDropdown"
           role="button"
           data-bs-toggle="dropdown"
           aria-expanded="false">
            <i class="bi bi-bell-fill"></i>  <!-- Changed from bi-bell to bi-bell-fill -->
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
                <?php if ($recentReminders->count() > 0): ?>
                    <?php foreach ($recentReminders as $reminder): ?>
                        <a href="<?= $this->Url->build(['controller' => 'Reminders', 'action' => 'index']) ?>"
                           class="reminder-item">
                            <div class="reminder-item-message">
                                <?= h($reminder->message) ?>
                            </div>
                            <div class="reminder-item-time">
                                <i class="bi bi-clock"></i>
                                <span>Scheduled: <?= $reminder->scheduled_at->format('g:i A') ?></span>
                                <?php if ($reminder->isOverdue()): ?>
                                    <span class="reminder-item-overdue">• Overdue</span>
                                <?php endif; ?>
                            </div>
                            <div class="reminder-item-time">
                                <small class="text-muted">
                                    <?php
                                    $diff = $now->diff($reminder->scheduled_at);
                                    if ($diff->d > 0) {
                                        echo $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
                                    } elseif ($diff->h > 0) {
                                        echo $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
                                    } elseif ($diff->i > 0) {
                                        echo $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
                                    } else {
                                        echo 'Just now';
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
