<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ContactMessage $contactMessage
 */
$this->assign('title', 'Contact Message Details');

echo $this->Html->css('view-shared', ['block' => true]);

$fullName = trim($contactMessage->first_name . ' ' . ($contactMessage->last_name ?: ''));
$initials = strtoupper(substr($contactMessage->first_name, 0, 1) . ($contactMessage->last_name ? substr($contactMessage->last_name, 0, 1) : ''));
$status = strtolower((string)$contactMessage->status);
$statusBadgeClass = 'message-status-badge status-' . $status;
?>

<style>
    .contact-message-page {
        max-width: 1320px;
        margin: 0 auto;
    }

    .contact-back-link {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        color: #6b7280;
        text-decoration: none;
        font-weight: 500;
        margin-bottom: 1.5rem;
    }

    .contact-back-link:hover {
        color: #0d6efd;
    }

    .contact-message-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, .06);
        padding: 2rem;
    }

    .contact-message-header {
        display: flex;
        justify-content: space-between;
        gap: 1.5rem;
        align-items: flex-start;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .contact-profile-summary {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .contact-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0d6efd, #6f42c1);
        color: #fff;
        font-size: 1.25rem;
        font-weight: 700;
        flex: 0 0 auto;
    }

    .contact-title-block h1 {
        margin: 0;
        font-size: 1.65rem;
        font-weight: 700;
        color: #111827;
    }

    .contact-meta-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .5rem .8rem;
        color: #6b7280;
        margin-top: .4rem;
    }

    .contact-meta-row a {
        color: #0d6efd;
    }

    .message-status-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: .25rem .65rem;
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .status-unread {
        color: #075985;
        background: #e0f2fe;
    }

    .status-read {
        color: #166534;
        background: #dcfce7;
    }

    .status-archived {
        color: #6b4e16;
        background: #fef3c7;
    }

    .contact-actions {
        display: flex;
        gap: .5rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .message-panel {
        margin-top: 1.5rem;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-left: 4px solid #0d6efd;
        border-radius: 12px;
        padding: 1.25rem 1.35rem;
    }

    .message-panel p:last-child {
        margin-bottom: 0;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-size: 1.15rem;
        font-weight: 700;
        margin: 1.75rem 0 .9rem;
        color: #111827;
    }

    .section-title i {
        color: #0d6efd;
    }

    .contact-detail-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }

    .contact-detail-item {
        background: #f8fafc;
        border: 1px solid #eef2f7;
        border-left: 4px solid #0d6efd;
        border-radius: 10px;
        padding: 1rem;
        min-height: 88px;
    }

    .contact-detail-label {
        display: flex;
        align-items: center;
        gap: .45rem;
        color: #6b7280;
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: .45rem;
    }

    .contact-detail-label i {
        color: #0d6efd;
    }

    .contact-detail-value {
        color: #111827;
        font-weight: 500;
        word-break: break-word;
    }

    .contact-detail-value small {
        margin-top: .15rem;
    }

    .reviewed-user-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .25rem .6rem;
        border-radius: 999px;
        background: #eef2ff;
        text-decoration: none;
        font-weight: 600;
    }

    .contact-alert {
        margin-top: 1.5rem;
        border-radius: 10px;
    }

    @media (max-width: 1100px) {
        .contact-detail-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .contact-message-card {
            padding: 1.25rem;
        }

        .contact-message-header {
            flex-direction: column;
        }

        .contact-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .contact-detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="contact-message-page">
    <?= $this->Html->link(
        '<i class="bi bi-arrow-left"></i> Back',
        $this->request->referer() ?: ['controller' => 'Dashboard', 'action' => 'staff'],
        ['class' => 'contact-back-link', 'escape' => false]
    ) ?>

    <div class="contact-message-card">
        <div class="contact-message-header">
            <div class="contact-profile-summary">
                <div class="contact-avatar"><?= h($initials) ?></div>
                <div class="contact-title-block">
                    <h1><?= h($fullName) ?></h1>
                    <div class="contact-meta-row">
                        <span><i class="bi bi-envelope"></i> <?= h($contactMessage->email) ?></span>
                        <span class="<?= h($statusBadgeClass) ?>"><?= h(ucfirst($contactMessage->status)) ?></span>
                        <span><i class="bi bi-hash"></i> ID: <?= $this->Number->format($contactMessage->id) ?></span>
                    </div>
                </div>
            </div>

            <div class="contact-actions">
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
                <?php else: ?>
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
                        'escape' => false
                    ]
                ) ?>
            </div>
        </div>

        <h2 class="section-title">
            <i class="bi bi-chat-left-text"></i> Message
        </h2>
        <div class="message-panel">
            <?= $this->Text->autoParagraph(h($contactMessage->message)); ?>
        </div>

        <h2 class="section-title">
            <i class="bi bi-person-lines-fill"></i> Contact Information
        </h2>
        <div class="contact-detail-grid">
            <div class="contact-detail-item">
                <div class="contact-detail-label"><i class="bi bi-person"></i> Full Name</div>
                <div class="contact-detail-value"><?= h($fullName) ?></div>
            </div>

            <div class="contact-detail-item">
                <div class="contact-detail-label"><i class="bi bi-envelope"></i> Email Address</div>
                <div class="contact-detail-value">
                    <a href="mailto:<?= h($contactMessage->email) ?>"><?= h($contactMessage->email) ?></a>
                </div>
            </div>

            <div class="contact-detail-item">
                <div class="contact-detail-label"><i class="bi bi-telephone"></i> Contact Number</div>
                <div class="contact-detail-value <?= $contactMessage->contact_number ? '' : 'text-muted' ?>">
                    <?php if ($contactMessage->contact_number): ?>
                        <a href="tel:<?= h($contactMessage->contact_number) ?>"><?= h($contactMessage->contact_number) ?></a>
                    <?php else: ?>
                        Not provided
                    <?php endif; ?>
                </div>
            </div>

            <div class="contact-detail-item">
                <div class="contact-detail-label"><i class="bi bi-info-circle"></i> Status</div>
                <div class="contact-detail-value">
                    <span class="<?= h($statusBadgeClass) ?>"><?= h(ucfirst($contactMessage->status)) ?></span>
                </div>
            </div>
        </div>

        <h2 class="section-title">
            <i class="bi bi-clock-history"></i> Submission Information
        </h2>
        <div class="contact-detail-grid">
            <div class="contact-detail-item">
                <div class="contact-detail-label"><i class="bi bi-calendar-check"></i> Submitted At</div>
                <div class="contact-detail-value">
                    <?= h($contactMessage->submitted_at->format('M d, Y g:i A')) ?>
                    <small class="text-muted d-block"><?= h($contactMessage->submitted_at->diffForHumans()) ?></small>
                </div>
            </div>

            <div class="contact-detail-item">
                <div class="contact-detail-label"><i class="bi bi-person-check"></i> Reviewed By</div>
                <div class="contact-detail-value <?= $contactMessage->reviewed_by ? '' : 'text-muted' ?>">
                    <?php if ($contactMessage->reviewed_by): ?>
                        <?= $this->Html->link(
                            '<i class="bi bi-person-badge"></i> User #' . h($contactMessage->reviewed_by),
                            ['controller' => 'Users', 'action' => 'view', $contactMessage->reviewed_by],
                            ['class' => 'reviewed-user-pill', 'escape' => false]
                        ) ?>
                    <?php else: ?>
                        Not yet reviewed
                    <?php endif; ?>
                </div>
            </div>

            <div class="contact-detail-item">
                <div class="contact-detail-label"><i class="bi bi-clock"></i> Reviewed At</div>
                <div class="contact-detail-value <?= $contactMessage->reviewed_at ? '' : 'text-muted' ?>">
                    <?php if ($contactMessage->reviewed_at): ?>
                        <?= h($contactMessage->reviewed_at->format('M d, Y g:i A')) ?>
                        <small class="text-muted d-block"><?= h($contactMessage->reviewed_at->diffForHumans()) ?></small>
                    <?php else: ?>
                        Not yet reviewed
                    <?php endif; ?>
                </div>
            </div>

            <div class="contact-detail-item">
                <div class="contact-detail-label"><i class="bi bi-hourglass"></i> Response Time</div>
                <div class="contact-detail-value <?= $contactMessage->reviewed_at ? '' : 'text-muted' ?>">
                    <?php if ($contactMessage->reviewed_at): ?>
                        <?= h($contactMessage->submitted_at->diffForHumans($contactMessage->reviewed_at, true)) ?>
                    <?php else: ?>
                        Pending response
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($contactMessage->status === 'unread'): ?>
            <div class="alert alert-info contact-alert">
                <i class="bi bi-info-circle"></i>
                <strong>Action Required:</strong> This message has not been reviewed yet. Please mark as read after addressing the inquiry.
            </div>
        <?php endif; ?>
    </div>
</div>
