<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ReminderSetting $settings
 */

$this->assign('title', 'Reminder Settings');

// CSS
$this->Html->css('reminders-settings', ['block' => true]);
$this->Html->css('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', ['block' => true]);

// JS
$this->Html->script('https://cdn.jsdelivr.net/npm/flatpickr', ['block' => true]);
$this->Html->script('reminders-settings', ['block' => true]);
?>

<div class="reminders-settings">
    <!-- Header Section -->
    <div class="mb-5 pb-3">
        <?= $this->element('page_header', [
            'title' => 'Reminder Settings',
            'subtitle' => 'Configure when you want to receive vital data reminders',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'patient']],
                ['title' => 'Reminders', 'url' => ['controller' => 'Reminders', 'action' => 'index']],
                ['title' => 'Settings', 'url' => null] // Current page
            ]
        ]) ?>
    </div>

    <?= $this->Form->create($settings, ['class' => 'settings-form']) ?>

    <!-- Enable/Disable Reminders -->
    <div class="section-card mb-4">
        <div class="section-header">
            <h3><i class="bi bi-toggle-on"></i> Status</h3>
        </div>
        <div class="p-4">
            <div class="switch-row">
                <div class="switch-label">
                    <strong>Enable Reminders</strong>
                    <br>
                    <small class="text-muted">Turn reminders on or off</small>
                </div>
                <div class="form-check form-switch">
                    <?= $this->Form->checkbox('is_active', [
                        'class' => 'form-check-input',
                        'role' => 'switch',
                        'id' => 'isActive'
                    ]) ?>
                    <label class="form-check-label" for="isActive"></label>
                </div>
            </div>
            <div class="alert alert-warning mt-3 mb-0">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Note:</strong> You will still see reminders in the "My Reminders" Page. Enable this to see them
                on the dashboard and notification badge.
            </div>
        </div>
    </div>

    <!-- Time Slots -->
    <div class="section-card mb-4">
        <div class="section-header">
            <h3><i class="bi bi-clock"></i> Daily Reminder Times</h3>
            <small class="text-muted">Set up to 4 reminder times per day</small>
        </div>
        <div class="p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="time-slot-1" class="form-label">
                        <i class="bi bi-alarm"></i> Reminder 1
                    </label>
                    <?php
                    $time1Value = '';
                    if (!empty($settings->time_slot_1)) {
                        if (is_object($settings->time_slot_1)) {
                            $time1Value = $settings->time_slot_1->format('H:i');
                        } elseif (is_string($settings->time_slot_1)) {
                            $parts = explode(':', $settings->time_slot_1);
                            $time1Value = $parts[0] . ':' . $parts[1];
                        }
                    }
                    ?>
                    <input
                        type="text"
                        name="time_slot_1"
                        id="time-slot-1"
                        class="form-control time-picker"
                        value="<?= h($time1Value) ?>"
                        placeholder="Select time"
                        readonly
                    >
                </div>

                <div class="col-md-6">
                    <label for="time-slot-2" class="form-label">
                        <i class="bi bi-alarm"></i> Reminder 2
                    </label>
                    <?php
                    $time2Value = '';
                    if (!empty($settings->time_slot_2)) {
                        if (is_object($settings->time_slot_2)) {
                            $time2Value = $settings->time_slot_2->format('H:i');
                        } elseif (is_string($settings->time_slot_2)) {
                            $parts = explode(':', $settings->time_slot_2);
                            $time2Value = $parts[0] . ':' . $parts[1];
                        }
                    }
                    ?>
                    <input
                        type="text"
                        name="time_slot_2"
                        id="time-slot-2"
                        class="form-control time-picker"
                        value="<?= h($time2Value) ?>"
                        placeholder="Select time"
                        readonly
                    >
                </div>

                <div class="col-md-6">
                    <label for="time-slot-3" class="form-label">
                        <i class="bi bi-alarm"></i> Reminder 3
                    </label>
                    <?php
                    $time3Value = '';
                    if (!empty($settings->time_slot_3)) {
                        if (is_object($settings->time_slot_3)) {
                            $time3Value = $settings->time_slot_3->format('H:i');
                        } elseif (is_string($settings->time_slot_3)) {
                            $parts = explode(':', $settings->time_slot_3);
                            $time3Value = $parts[0] . ':' . $parts[1];
                        }
                    }
                    ?>
                    <input
                        type="text"
                        name="time_slot_3"
                        id="time-slot-3"
                        class="form-control time-picker"
                        value="<?= h($time3Value) ?>"
                        placeholder="Select time"
                        readonly
                    >
                </div>

                <div class="col-md-6">
                    <label for="time-slot-4" class="form-label">
                        <i class="bi bi-alarm"></i> Reminder 4
                    </label>
                    <?php
                    $time4Value = '';
                    if (!empty($settings->time_slot_4)) {
                        if (is_object($settings->time_slot_4)) {
                            $time4Value = $settings->time_slot_4->format('H:i');
                        } elseif (is_string($settings->time_slot_4)) {
                            $parts = explode(':', $settings->time_slot_4);
                            $time4Value = $parts[0] . ':' . $parts[1];
                        }
                    }
                    ?>
                    <input
                        type="text"
                        name="time_slot_4"
                        id="time-slot-4"
                        class="form-control time-picker"
                        value="<?= h($time4Value) ?>"
                        placeholder="Select time"
                        readonly
                    >
                </div>
            </div>

            <div class="alert alert-info mt-3 mb-0">
                <i class="bi bi-info-circle"></i>
                Click on a time field to open the time picker.
            </div>
        </div>
    </div>

    <!-- Notification Methods -->
    <div class="section-card mb-4">
        <div class="section-header">
            <h3><i class="bi bi-bell"></i> Notification Methods</h3>
            <small class="text-muted">How you want to receive reminders</small>
        </div>
        <div class="p-4">
            <div class="switch-row">
                <div class="switch-label">
                    <i class="bi bi-envelope text-primary"></i> <strong>Email Notifications</strong>
                    <br>
                    <small class="text-muted">Receive reminder emails at scheduled times</small>
                </div>
                <div class="form-check form-switch">
                    <?= $this->Form->checkbox('email_enabled', [
                        'class' => 'form-check-input',
                        'role' => 'switch',
                        'id' => 'emailEnabled'
                    ]) ?>
                    <label class="form-check-label" for="emailEnabled"></label>
                </div>
            </div>
        </div>
    </div>

    <!-- Recurrence Pattern (Hidden for now, default to 'daily') -->
    <?= $this->Form->hidden('recurrence_pattern', ['value' => 'daily']) ?>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <?= $this->Html->link(
                'Cancel',
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary']
            ) ?>
        </div>
        <div>
            <?= $this->Form->button(
                '<i class="bi bi-check-circle"></i> Save Settings',
                [
                    'type' => 'submit',
                    'class' => 'btn btn-primary',
                    'escapeTitle' => false
                ]
            ) ?>
        </div>
    </div>

    <?= $this->Form->end() ?>
</div>
