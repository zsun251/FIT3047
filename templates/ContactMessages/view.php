<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ContactMessage $contactMessage
 */
$this->assign('title', 'Contact Message Details');

// CSS
echo $this->Html->css('view-shared', ['block' => true]);
echo $this->Html->css('contact-message-view', ['block' => true]);

// Get initials for avatar
$initials = strtoupper(substr($contactMessage->first_name, 0, 1) . ($contactMessage->last_name ? substr($contactMessage->last_name, 0, 1) : ''));

// Status badge class
$statusBadgeClass = 'badge-' . strtolower($contactMessage->status);

?>

<!-- Back Link -->
<?= $this->Html->link(
    '<i class="bi bi-arrow-left"></i> Back',
    $this->request->referer() ?: ['controller' => 'Dashboard', 'action' => 'staff'],
    ['class' => 'back-link', 'escape' => false]
) ?>

<div class="view-container">
    <!-- Header -->
    <div class="view-header">
        <div class="user-header">
            <div class="user-avatar"><?= $initials ?></div>
            <div class="user-info">
                <h1><?= h($contactMessage->first_name . ' ' . ($contactMessage->last_name ?: '')) ?></h1>
                <div class="user-meta">
                    <span class="user-meta-item">
                        <i class="bi bi-envelope"></i> <?= h($contactMessage->email) ?>
                    </span>
                    <span class="<?= $statusBadgeClass ?>"><?= h(ucfirst($contactMessage->status)) ?></span>
                    <span class="user-meta-item">
                        <i class="bi bi-hash"></i> ID: <?= $this->Number->format($contactMessage->id) ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="view-actions">
            <?php if ($contactMessage->status === 'unread'): ?>
                <?= $this->Form->postLink(
                    '<i class="bi bi-check-circle"></i> Mark as Read',
                    ['action' => 'updateStatus', $contactMessage->id, 'read'],
                    ['class' => 'btn btn-success', 'escape' => false]
                ) ?>
            <?php endif; ?>

            <?php if ($contactMessage->status === 'read'): ?>
                <?= $this->Form->postLink(
                    '<i class="bi bi-arrow-counterclockwise"></i> Mark as Unread',
                    ['action' => 'updateStatus', $contactMessage->id, 'unread'],
                    ['class' => 'btn btn-outline-secondary', 'escape' => false]
                ) ?>
            <?php endif; ?>

            <?php if ($contactMessage->status !== 'archived'): ?>
                <?= $this->Form->postLink(
                    '<i class="bi bi-archive"></i> Archive',
                    ['action' => 'updateStatus', $contactMessage->id, 'archived'],
                    ['class' => 'btn btn-outline-secondary', 'escape' => false]
                ) ?>
            <?php endif; ?>

            <?php if ($contactMessage->status === 'archived'): ?>
                <?= $this->Form->postLink(
                    '<i class="bi bi-inbox"></i> Unarchive',
                    ['action' => 'updateStatus', $contactMessage->id, 'read'],
                    ['class' => 'btn btn-outline-secondary', 'escape' => false]
                ) ?>
            <?php endif; ?>

            <?= $this->Form->postLink(
                '<i class="bi bi-trash"></i> Delete',
                ['action' => 'delete', $contactMessage->id],
                [
                    'confirm' => __('Are you sure you want to delete this message?'),
                    'class' => 'btn btn-outline-danger',
                    'escape' => false]
            ) ?>
        </div>
    </div>

    <!-- Message Content -->
    <h2 class="section-title mb-3">
        <i class="bi bi-chat-left-text"></i> Message
    </h2>
    <div class="message-content">
        <blockquote class="contact-message-text">
            <?= $this->Text->autoParagraph(h($contactMessage->message)); ?>
        </blockquote>
    </div>

    <!-- Contact Information -->
    <h2 class="section-title mb-3 mt-4">
        <i class="bi bi-person-lines-fill"></i> Contact Information
    </h2>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label">
                <i class="bi bi-person"></i> Full Name
            </div>
            <div class="detail-value">
                <?= h($contactMessage->first_name . ' ' . ($contactMessage->last_name ?: '')) ?>
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">
                <i class="bi bi-envelope"></i> Email Address
            </div>
            <div class="detail-value">
                <a href="mailto:<?= h($contactMessage->email) ?>" class="email-link">
                    <?= h($contactMessage->email) ?>
                </a>
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">
                <i class="bi bi-telephone"></i> Contact Number
            </div>
            <div class="detail-value <?= $contactMessage->contact_number ? '' : 'text-muted' ?>">
                <?php if ($contactMessage->contact_number): ?>
                    <a href="tel:<?= h($contactMessage->contact_number) ?>">
                        <?= h($contactMessage->contact_number) ?>
                    </a>
                <?php else: ?>
                    Not provided
                <?php endif; ?>
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">
                <i class="bi bi-info-circle"></i> Status
            </div>
            <div class="detail-value">
                <span class="<?= $statusBadgeClass ?>"><?= h(ucfirst($contactMessage->status)) ?></span>
            </div>
        </div>
    </div>

    <!-- Submission Information -->
    <h2 class="section-title mb-3 mt-4">
        <i class="bi bi-clock-history"></i> Submission Information
    </h2>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label">
                <i class="bi bi-calendar-check"></i> Submitted At
            </div>
            <div class="detail-value">
                <?= h($contactMessage->submitted_at->format('M d, Y g:i A')) ?>
                <small class="text-muted d-block"><?= h($contactMessage->submitted_at->diffForHumans()) ?></small>
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">
                <i class="bi bi-person-check"></i> Reviewed By
            </div>
            <div class="detail-value <?= $contactMessage->reviewed_by ? '' : 'text-muted' ?>">
                <?php if ($contactMessage->reviewed_by): ?>
                    <?= $this->Html->link(
                        'User #' . $contactMessage->reviewed_by,
                        ['controller' => 'Users', 'action' => 'view', $contactMessage->reviewed_by]
                    ) ?>
                <?php else: ?>
                    Not yet reviewed
                <?php endif; ?>
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">
                <i class="bi bi-clock"></i> Reviewed At
            </div>
            <div class="detail-value <?= $contactMessage->reviewed_at ? '' : 'text-muted' ?>">
                <?php if ($contactMessage->reviewed_at): ?>
                    <?= h($contactMessage->reviewed_at->format('M d, Y g:i A')) ?>
                    <small class="text-muted d-block"><?= h($contactMessage->reviewed_at->diffForHumans()) ?></small>
                <?php else: ?>
                    Not yet reviewed
                <?php endif; ?>
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">
                <i class="bi bi-hourglass"></i> Response Time
            </div>
            <div class="detail-value <?= $contactMessage->reviewed_at ? '' : 'text-muted' ?>">
                <?php if ($contactMessage->reviewed_at): ?>
                    <?= h($contactMessage->submitted_at->diffForHumans($contactMessage->reviewed_at, true)) ?>
                <?php else: ?>
                    Pending response
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <?php if ($contactMessage->status === 'unread'): ?>
        <div class="alert alert-info mt-4">
            <i class="bi bi-info-circle"></i>
            <strong>Action Required:</strong> This message has not been reviewed yet.
            Please mark as read after addressing the inquiry.
        </div>
    <?php endif; ?>
</div>
