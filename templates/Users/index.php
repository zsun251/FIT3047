<?php
declare(strict_types=1);

/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\User>|array $users
 */

$this->assign('title', __('User Management'));

$this->Html->css([
    'users-index',
], ['block' => 'css']);

$users = $users ?? [];
$userList = is_array($users) ? $users : iterator_to_array($users);

$normalizeType = function ($user): string {
    $type = strtolower(trim((string)($user->type ?? $user->user_type ?? $user->role ?? '')));

    return match ($type) {
        'admin', 'administrator' => 'admin',
        'clinician', 'doctor', 'staff' => 'clinician',
        'patient' => 'patient',
        default => $type ?: 'unknown',
    };
};

$getTypeLabel = function (string $type): string {
    return match ($type) {
        'admin' => 'Admin',
        'clinician' => 'Clinician',
        'patient' => 'Patient',
        default => 'Unknown',
    };
};

$getFullName = function ($user): string {
    $firstName = trim((string)($user->first_name ?? ''));
    $lastName = trim((string)($user->last_name ?? ''));
    $fullName = trim($firstName . ' ' . $lastName);

    return $fullName !== '' ? $fullName : 'User #' . (int)$user->id;
};

$getInitials = function (string $name): string {
    $parts = preg_split('/\s+/', trim($name));
    $initials = '';

    foreach ($parts as $part) {
        if ($part !== '') {
            $initials .= strtoupper(substr($part, 0, 1));
        }
    }

    return substr($initials ?: 'U', 0, 2);
};

$formatDate = function ($date): string {
    if ($date instanceof DateTimeInterface) {
        return $date->format('M d, Y');
    }

    return '—';
};

$getDateOfBirth = function ($user) use ($formatDate): string {
    return $formatDate($user->date_of_birth ?? $user->dob ?? null);
};

$getRegisteredDate = function ($user) use ($formatDate): string {
    return $formatDate($user->created_at ?? null);
};

$stats = [
    'total' => count($userList),
    'admin' => 0,
    'clinician' => 0,
    'patient' => 0,
];

foreach ($userList as $user) {
    $type = $normalizeType($user);

    if (array_key_exists($type, $stats)) {
        $stats[$type]++;
    }
}

$backUrl = $this->request->referer(true);

if (!$backUrl || str_contains($backUrl, '/users')) {
    $backUrl = ['controller' => 'Dashboard', 'action' => 'staff'];
}
?>

<div class="users-page">

    <div class="rc-dashboard-page-header users-page-header">
        <div class="rc-dashboard-header-main users-header-main">
            <div class="rc-dashboard-header-copy users-header-copy">
                <div class="rc-dashboard-title-row users-title-row">
                    <?= $this->Html->link(
                        '<i class="bi bi-arrow-left"></i>',
                        $backUrl,
                        [
                            'class' => 'rc-back-button users-back-button',
                            'escape' => false,
                            'aria-label' => __('Return to dashboard'),
                        ],
                    ) ?>

                    <h1 class="rc-dashboard-title"><?= __('User Management') ?></h1>
                </div>

                <p class="rc-dashboard-subtitle">
                    <?= __('Manage system users and their roles') ?>
                </p>
            </div>

            <?= $this->Html->link(
                '<i class="bi bi-plus-circle"></i><span>' . __('New User') . '</span>',
                ['action' => 'add'],
                [
                    'class' => 'rc-action-pill users-main-button',
                    'escape' => false,
                ],
            ) ?>
        </div>
    </div>

    <div class="users-stats-grid">
        <div class="users-stat-card">
            <div class="users-stat-icon blue">
                <i class="bi bi-people"></i>
            </div>

            <div>
                <span><?= __('Total Users') ?></span>
                <h2><?= h($stats['total']) ?></h2>
                <p><?= __('All registered users') ?></p>
            </div>
        </div>

        <div class="users-stat-card">
            <div class="users-stat-icon purple">
                <i class="bi bi-shield-check"></i>
            </div>

            <div>
                <span><?= __('Admins') ?></span>
                <h2><?= h($stats['admin']) ?></h2>
                <p><?= __('System administrators') ?></p>
            </div>
        </div>

        <div class="users-stat-card">
            <div class="users-stat-icon green">
                <i class="bi bi-person-badge"></i>
            </div>

            <div>
                <span><?= __('Clinicians') ?></span>
                <h2><?= h($stats['clinician']) ?></h2>
                <p><?= __('Healthcare staff') ?></p>
            </div>
        </div>

        <div class="users-stat-card">
            <div class="users-stat-icon orange">
                <i class="bi bi-person-heart"></i>
            </div>

            <div>
                <span><?= __('Patients') ?></span>
                <h2><?= h($stats['patient']) ?></h2>
                <p><?= __('Registered patients') ?></p>
            </div>
        </div>
    </div>

    <div class="users-panel">

        <div class="users-toolbar">
            <div class="users-tabs">
                <button type="button" class="user-filter active" data-filter="all">
                    <?= __('All Users') ?>
                </button>
                <button type="button" class="user-filter" data-filter="admin">
                    <?= __('Admins') ?>
                </button>
                <button type="button" class="user-filter" data-filter="clinician">
                    <?= __('Clinicians') ?>
                </button>
                <button type="button" class="user-filter" data-filter="patient">
                    <?= __('Patients') ?>
                </button>
            </div>

            <div class="users-search">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    id="userSearchInput"
                    placeholder="<?= h(__('Search users...')) ?>"
                >
            </div>
        </div>

        <?php if (!empty($userList)): ?>
            <div class="users-table">
                <div class="users-table-head">
                    <div><?= __('ID') ?></div>
                    <div><?= __('User') ?></div>
                    <div><?= __('Contact') ?></div>
                    <div><?= __('Type') ?></div>
                    <div><?= __('Date of Birth') ?></div>
                    <div><?= __('Registered') ?></div>
                    <div><?= __('Actions') ?></div>
                </div>

                <?php foreach ($userList as $user): ?>
                    <?php
                    $type = $normalizeType($user);
                    $typeLabel = $getTypeLabel($type);
                    $fullName = $getFullName($user);
                    $initials = $getInitials($fullName);
                    $username = $user->username ?? 'user';
                    $email = $user->email ?? null;
                    $phone = $user->phone ?? null;
                    $phoneHref = $phone ? preg_replace('/[^0-9+]/', '', (string)$phone) : null;

                    $searchText = strtolower(
                        $fullName . ' ' .
                        $username . ' ' .
                        ($email ?? '') . ' ' .
                        ($phone ?? '') . ' ' .
                        $typeLabel . ' ' .
                        $user->id
                    );
                    ?>

                    <div
                        class="user-row"
                        data-type="<?= h($type) ?>"
                        data-search="<?= h($searchText) ?>"
                    >

                        <div class="user-id">
                            <span class="rc-mini-pill">#<?= h($user->id) ?></span>
                        </div>

                        <div class="user-main">
                            <div class="user-avatar <?= h($type) ?>">
                                <?= h($initials) ?>
                            </div>

                            <div>
                                <div class="user-name">
                                    <?= h($fullName) ?>
                                </div>

                                <div class="user-username">
                                    @<?= h($username) ?>
                                </div>
                            </div>
                        </div>

                        <div class="user-contact">
                            <div>
                                <i class="bi bi-envelope"></i>

                                <?php if ($email): ?>
                                    <a href="mailto:<?= h($email) ?>">
                                        <?= h($email) ?>
                                    </a>
                                <?php else: ?>
                                    <span>—</span>
                                <?php endif; ?>
                            </div>

                            <div>
                                <i class="bi bi-telephone"></i>

                                <?php if ($phone && $phoneHref): ?>
                                    <a href="tel:<?= h($phoneHref) ?>">
                                        <?= h($phone) ?>
                                    </a>
                                <?php else: ?>
                                    <span>—</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div>
                            <span class="user-type-badge <?= h($type) ?>">
                                <?= h($typeLabel) ?>
                            </span>
                        </div>

                        <div class="user-date">
                            <?= h($getDateOfBirth($user)) ?>
                        </div>

                        <div class="user-date">
                            <?= h($getRegisteredDate($user)) ?>
                        </div>

                        <div class="user-actions">
                            <?= $this->Html->link(
                                '<i class="bi bi-eye"></i>',
                                ['action' => 'view', $user->id],
                                [
                                    'class' => 'rc-icon-action user-action-btn view',
                                    'escape' => false,
                                    'title' => __('View'),
                                    'aria-label' => __('View user'),
                                ],
                            ) ?>

                            <?= $this->Html->link(
                                '<i class="bi bi-pencil"></i>',
                                ['action' => 'edit', $user->id],
                                [
                                    'class' => 'rc-icon-action rc-icon-action-green user-action-btn edit',
                                    'escape' => false,
                                    'title' => __('Edit'),
                                    'aria-label' => __('Edit user'),
                                ],
                            ) ?>

                            <?= $this->Form->postLink(
                                '<i class="bi bi-trash"></i>',
                                ['action' => 'delete', $user->id],
                                [
                                    'class' => 'rc-icon-action rc-icon-action-danger user-action-btn delete',
                                    'escape' => false,
                                    'confirm' => __('Are you sure you want to delete user #{0}?', $user->id),
                                    'title' => __('Delete'),
                                    'aria-label' => __('Delete user'),
                                ],
                            ) ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <div class="users-footer">
                <?= __('Showing {0} user{1}', h(count($userList)), count($userList) === 1 ? '' : 's') ?>
            </div>
        <?php else: ?>
            <div class="users-empty-state">
                <div class="users-empty-icon">
                    <i class="bi bi-person-x"></i>
                </div>

                <h3><?= __('No users found') ?></h3>
                <p><?= __('There are currently no user records to display.') ?></p>
            </div>
        <?php endif; ?>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filters = document.querySelectorAll('.user-filter');
        const rows = document.querySelectorAll('.user-row');
        const searchInput = document.getElementById('userSearchInput');
        let activeFilter = 'all';

        function applyFilters() {
            const searchValue = searchInput ? searchInput.value.toLowerCase().trim() : '';

            rows.forEach(function (row) {
                const type = row.dataset.type;
                const searchText = row.dataset.search || '';
                const typeMatch = activeFilter === 'all' || type === activeFilter;
                const searchMatch = searchText.includes(searchValue);

                row.style.display = typeMatch && searchMatch ? 'grid' : 'none';
            });
        }

        filters.forEach(function (button) {
            button.addEventListener('click', function () {
                filters.forEach(function (btn) {
                    btn.classList.remove('active');
                });

                button.classList.add('active');
                activeFilter = button.dataset.filter;
                applyFilters();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }
    });
</script>
