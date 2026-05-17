<?php
/**
 * Reminders Bell Icon - Mobile Version
 *
 * Simplified bell icon for mobile topbar (left of hamburger)
 * Clicking goes directly to reminders page
 *
 * @var \App\View\AppView $this
 */

use App\Policy\UserAccess;
use Cake\ORM\TableRegistry;

// Get reminders data server-side
$remindersTable = TableRegistry::getTableLocator()->get('Reminders');
$identity = $this->request->getAttribute('identity');
$patientId = UserAccess::getPatientId($identity);

// Check if reminders are enabled
$reminderSettingsTable = TableRegistry::getTableLocator()->get('ReminderSettings');
$settings = $reminderSettingsTable->getPatientSettings($patientId);

// Only show count if reminders are active
if ($settings && $settings->is_active) {
    // Use countActive to get today's active reminders only
    $reminderCount = $remindersTable->countActive($patientId);
} else {
    $reminderCount = 0;
}
?>

<?php if ($settings && $settings->is_active): ?>
    <a href="<?= $this->Url->build(['controller' => 'Reminders', 'action' => 'index']) ?>"
       class="mobile-reminder-bell position-relative d-flex align-items-center justify-content-center"
       aria-label="View reminders<?= $reminderCount > 0 ? " ({$reminderCount} active)" : '' ?>">
        <i class="bi bi-bell-fill"></i>
        <?php if ($reminderCount > 0): ?>
            <span class="badge bg-danger rounded-pill position-absolute mobile-reminder-badge pulse">
                <?= $reminderCount ?>
            </span>
        <?php endif; ?>
    </a>
<?php endif; ?>
