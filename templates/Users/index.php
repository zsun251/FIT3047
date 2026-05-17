<?php
declare(strict_types=1);

$this->assign('title', 'User Management');

$this->Html->css([
    'users-index',
], ['block' => 'css']);

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

    return $fullName !== '' ? $fullName : 'User #' . $user->id;
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

$getDateOfBirth = function ($user): string {
    $date = $user->date_of_birth ?? $user->dob ?? null;

    return $date ? $date->format('M d, Y') : '—';
};

$getRegisteredDate = function ($user): string {
    $date = $user->created_at ?? null;

    return $date ? $date->format('M d, Y') : '—';
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

$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => ['controller' => 'Dashboard', 'action' => 'staff']],
    ['title' => 'Users', 'url' => null],
];
?>

<?= $this->element('page_header', [
    'breadcrumbs' => $breadcrumbs,
]) ?>

<div class="users-page">

    <div class="users-header">
        <div>
            <h1>User Management</h1>
            <p>Manage system users and their roles</p>
        </div>

        <?= $this->Html->link(
            '<i class="bi bi-plus-circle"></i> New User',
            ['action' => 'add'],
            [
                'class' => 'users-main-btn',
                'escape' => false,
            ]
        ) ?>
    </div>

    <div class="users-stats-grid">
        <div class="users-stat-card">
            <div class="users-stat-icon blue">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <span>Total Users</span>
                <h2><?= h($stats['total']) ?></h2>
                <p>All registered users</p>
            </div>
        </div>

        <div class="users-stat-card">
            <div class="users-stat-icon purple">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <span>Admins</span>
                <h2><?= h($stats['admin']) ?></h2>
                <p>System administrators</p>
            </div>
        </div>

        <div class="users-stat-card">
            <div class="users-stat-icon green">
                <i class="bi bi-person-badge"></i>
            </div>
            <div>
                <span>Clinicians</span>
                <h2><?= h($stats['clinician']) ?></h2>
                <p>Healthcare staff</p>
            </div>
        </div>

        <div class="users-stat-card">
            <div class="users-stat-icon orange">
                <i class="bi bi-person-heart"></i>
            </div>
            <div>
                <span>Patients</span>
                <h2><?= h($stats['patient']) ?></h2>
                <p>Registered patients</p>
            </div>
        </div>
    </div>

    <div class="users-panel">

        <div class="users-toolbar">
            <div class="users-tabs">
                <button type="button" class="user-filter active" data-filter="all">All Users</button>
                <button type="button" class="user-filter" data-filter="admin">Admins</button>
                <button type="button" class="user-filter" data-filter="clinician">Clinicians</button>
                <button type="button" class="user-filter" data-filter="patient">Patients</button>
            </div>

            <div class="users-search">
                <i class="bi bi-search"></i>
                <input type="text" id="userSearchInput" placeholder="Search users...">
            </div>
        </div>

        <?php if (!empty($userList)): ?>
            <div class="users-table">
                <div class="users-table-head">
                    <div>ID</div>
                    <div>User</div>
                    <div>Contact</div>
                    <div>Type</div>
                    <div>Date of Birth</div>
                    <div>Registered</div>
                    <div>Actions</div>
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
                    $searchText = strtolower(
                        $fullName . ' ' .
                        $username . ' ' .
                        ($email ?? '') . ' ' .
                        ($phone ?? '') . ' ' .
                        $typeLabel . ' ' .
                        $user->id
                    );
                    ?>

                    <div class="user-row"
                         data-type="<?= h($type) ?>"
                         data-search="<?= h($searchText) ?>">

                        <div class="user-id">
                            #<?= h($user->id) ?>
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
                                    <a href="mailto:<?= h($email) ?>"><?= h($email) ?></a>
                                <?php else: ?>
                                    <span>—</span>
                                <?php endif; ?>
                            </div>

                            <div>
                                <i class="bi bi-telephone"></i>
                                <?php if ($phone): ?>
                                    <a href="tel:<?= h($phone) ?>"><?= h($phone) ?></a>
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
                                    'class' => 'user-action-btn view',
                                    'escape' => false,
                                    'title' => 'View',
                                ]
                            ) ?>

                            <?= $this->Html->link(
                                '<i class="bi bi-pencil"></i>',
                                ['action' => 'edit', $user->id],
                                [
                                    'class' => 'user-action-btn edit',
                                    'escape' => false,
                                    'title' => 'Edit',
                                ]
                            ) ?>

                            <?= $this->Form->postLink(
                                '<i class="bi bi-trash"></i>',
                                ['action' => 'delete', $user->id],
                                [
                                    'class' => 'user-action-btn delete',
                                    'escape' => false,
                                    'confirm' => __('Are you sure you want to delete user #{0}?', $user->id),
                                    'title' => 'Delete',
                                ]
                            ) ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <div class="users-footer">
                Showing <?= h(count($userList)) ?> user<?= count($userList) === 1 ? '' : 's' ?>
            </div>
        <?php else: ?>
            <div class="users-empty-state">
                <div class="users-empty-icon">
                    <i class="bi bi-person-x"></i>
                </div>
                <h3>No users found</h3>
                <p>There are currently no user records to display.</p>
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
