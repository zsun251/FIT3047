<?php
use App\Policy\UserAccess;

// Current user and request details
$identity = $this->request->getAttribute('identity');
$currentController = $this->request->getParam('controller');
$currentAction = $this->request->getParam('action');
$currentPage = $this->request->getParam('pass.0');

// User role checks
$isLoggedIn = $identity !== null;
$isStaff = $identity && UserAccess::isStaff($identity);
$isPatient = $identity && UserAccess::isPatient($identity);
$isAdmin = $identity && UserAccess::isAdmin($identity);
$isClinician = $identity && UserAccess::isClinician($identity);

// Navigation context: topbar, sidebar, or mobile menu
$context = $context ?? 'topbar';
$linkClass = $context === 'sidebar' ? 'sidebar-link' : 'nav-link';
$itemClass = $context === 'sidebar' ? '' : 'nav-item';

// Sidebar uses one section heading
$showSections = $context === 'sidebar';

// Add icons to sidebar links, and mobile topbar links
$linkLabel = function (string $label, string $icon, bool $showText = true) use ($context): string {
    if ($context === 'sidebar') {
        return '<i class="bi ' . h($icon) . '"></i><span>' . h($label) . '</span>';
    }

    if ($context === 'topbar' && !$showText) {
        return '<i class="bi ' . h($icon) . '"></i><span class="visually-hidden">' . h($label) . '</span>';
    }

    // Mobile or with text
    return '<i class="bi ' . h($icon) . ' topbar-mobile-icon"></i><span>' . h($label) . '</span>';
};
// Patients browse resources, staff manage resources
$resourcesRoute = ['controller' => 'Resources', 'action' => 'browse'];
if ($isStaff || $isClinician || $isAdmin) {
    $resourcesRoute = ['controller' => 'Resources', 'action' => 'index'];
}

// Role-specific dashboard route
$dashboardRoute = null;
$dashboardActive = false;
if ($isStaff) {
    $dashboardRoute = ['controller' => 'Dashboard', 'action' => 'staff'];
    $dashboardActive = $currentController === 'Dashboard' && $currentAction === 'staff';
} elseif ($isPatient) {
    $dashboardRoute = ['controller' => 'Dashboard', 'action' => 'patient'];
    $dashboardActive = $currentController === 'Dashboard' && $currentAction === 'patient';
}
?>



<?php if ($context === 'topbar'): ?>
    <!-- PATIENT REMINDERS NOTIFICATION DROPDOWN -->
    <?php if ($isPatient): ?>
        <?= $this->element('reminders_dropdown') ?>
    <?php endif; ?>

    <!-- Desktop topbar links -->
    <li class="<?= $itemClass ?> topbar-always-show">
        <?= $this->Html->link($linkLabel('Home', 'bi-house'), ['controller' => 'Pages', 'action' => 'display', 'home'], ['class' => $linkClass . ($currentController === 'Pages' && $currentPage === 'home' ? ' active' : ''), 'escape' => false]) ?>
    </li>

    <?php if (!$isLoggedIn || $isPatient): ?>
        <li class="<?= $itemClass ?> topbar-always-show">
            <?= $this->Html->link($linkLabel('Contact', 'bi-envelope'), ['controller' => 'ContactMessages', 'action' => 'add'], ['class' => $linkClass . ($currentController === 'ContactMessages' && $currentAction === 'add' ? ' active' : ''), 'escape' => false]) ?>
        </li>
    <?php endif; ?>

    <?php if ($isPatient): ?>
        <li class="<?= $itemClass ?> topbar-always-show">
            <?= $this->Html->link($linkLabel('Resources', 'bi-journal-medical'), $resourcesRoute, ['class' => $linkClass . ($currentController === 'Resources' ? ' active' : ''), 'escape' => false]) ?>
        </li>
    <?php endif; ?>

    <?php if ($isLoggedIn && $dashboardRoute): ?>
        <li class="<?= $itemClass ?> topbar-always-show">
            <?= $this->Html->link($linkLabel('Dashboard', 'bi-grid'), $dashboardRoute, ['class' => $linkClass . ($dashboardActive ? ' active' : ''), 'escape' => false]) ?>
        </li>
    <?php endif; ?>
<?php endif; ?>



<?php if ($context === 'mobile'): ?>
    <!-- Mobile menu combines topbar and sidebar links -->
    <li class="<?= $itemClass ?> topbar-always-show">
        <?= $this->Html->link($linkLabel('Home', 'bi-house'), ['controller' => 'Pages', 'action' => 'display', 'home'], ['class' => $linkClass . ($currentController === 'Pages' && $currentPage === 'home' ? ' active' : ''), 'escape' => false]) ?>
    </li>

    <?php if (!$isLoggedIn || $isPatient): ?>
        <li class="<?= $itemClass ?> topbar-always-show">
            <?= $this->Html->link($linkLabel('Contact', 'bi-envelope'), ['controller' => 'ContactMessages', 'action' => 'add'], ['class' => $linkClass . ($currentController === 'ContactMessages' && $currentAction === 'add' ? ' active' : ''), 'escape' => false]) ?>
        </li>
    <?php endif; ?>

    <?php if ($isLoggedIn && $dashboardRoute): ?>
        <li class="<?= $itemClass ?> topbar-always-show">
            <?= $this->Html->link($linkLabel('Dashboard', 'bi-grid'), $dashboardRoute, ['class' => $linkClass . ($dashboardActive ? ' active' : ''), 'escape' => false]) ?>
        </li>

        <li class="<?= $itemClass ?> topbar-always-show">
            <?= $this->Html->link($linkLabel('Resources', 'bi-journal-medical'), $resourcesRoute, ['class' => $linkClass . ($currentController === 'Resources' ? ' active' : ''), 'escape' => false]) ?>
        </li>
    <?php endif; ?>

    <?php if ($isStaff): ?>


        <!-- Staff mobile links -->
        <?php if ($isAdmin): ?>
            <li class="<?= $itemClass ?> topbar-always-show">
                <?= $this->Html->link($linkLabel('Users', 'bi-people'), ['controller' => 'Users', 'action' => 'index'], ['class' => $linkClass . ($currentController === 'Users' && $currentAction === 'index' ? ' active' : ''), 'escape' => false]) ?>
            </li>
        <?php endif; ?>

        <?php if ($isAdmin || $isClinician): ?>
            <li class="<?= $itemClass ?> topbar-always-show">
                <?= $this->Html->link($linkLabel('Patients', 'bi-person-check'), ['controller' => 'Patients', 'action' => 'index'], ['class' => $linkClass . ($currentController === 'Patients' ? ' active' : ''), 'escape' => false]) ?>
            </li>

            <li class="<?= $itemClass ?> topbar-always-show">
                <?= $this->Html->link($linkLabel('Appointments', 'bi-calendar3'), ['controller' => 'Appointments', 'action' => 'index'], ['class' => $linkClass . ($currentController === 'Appointments' ? ' active' : ''), 'escape' => false]) ?>
            </li>

            <li class="<?= $itemClass ?> topbar-always-show">
                <?= $this->Html->link($linkLabel('Health Records', 'bi-heart-pulse'), ['controller' => 'VitalData', 'action' => 'index'], ['class' => $linkClass . ($currentController === 'VitalData' ? ' active' : ''), 'escape' => false]) ?>
            </li>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($isPatient): ?>


        <!-- Patient mobile links -->
        <li class="<?= $itemClass ?> topbar-always-show">
            <?= $this->Html->link($linkLabel('Appointments', 'bi-calendar3'), ['controller' => 'Appointments', 'action' => 'upcomingAppointments'], ['class' => $linkClass . ($currentController === 'Appointments' && $currentAction === 'upcomingAppointments' ? ' active' : ''), 'escape' => false]) ?>
        </li>
        <li class="<?= $itemClass ?> topbar-always-show">
            <?= $this->Html->link($linkLabel('Health Records', 'bi-heart-pulse'), ['controller' => 'VitalData', 'action' => 'patientHistory'], ['class' => $linkClass . ($currentController === 'VitalData' && $currentAction === 'patientHistory' ? ' active' : ''), 'escape' => false]) ?>
        </li>
        <li class="<?= $itemClass ?> topbar-always-show">
            <?= $this->Html->link($linkLabel('Reminders', 'bi-bell'), ['controller' => 'Reminders', 'action' => 'index'], ['class' => $linkClass . ($currentController === 'Reminders' ? ' active' : ''), 'escape' => false]) ?>
            <span id="reminder-badge" class="badge"></span>
        </li>

        <li class="<?= $itemClass ?> topbar-always-show">
            <?= $this->Html->link($linkLabel('Profile', 'bi-person'), ['controller' => 'Users', 'action' => 'edit', $identity ? $identity->getIdentifier() : null], ['class' => $linkClass . ($currentController === 'Users' && $currentAction === 'edit' ? ' active' : ''), 'escape' => false]) ?>
        </li>
    <?php endif; ?>
<?php endif; ?>



<?php if ($context === 'sidebar' && $isLoggedIn): ?>


    <!-- Desktop sidebar links -->
    <?php if ($showSections): ?><div class="sidebar-section-title">Navigation</div><?php endif; ?>

    <?php if ($dashboardRoute): ?>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link($linkLabel('Dashboard', 'bi-grid'), $dashboardRoute, ['class' => $linkClass . ($dashboardActive ? ' active' : ''), 'escape' => false]) ?>
        </li>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link($linkLabel('Resources', 'bi-journal-medical'), $resourcesRoute, ['class' => $linkClass . ($currentController === 'Resources' ? ' active' : ''), 'escape' => false]) ?>
        </li>
    <?php endif; ?>

    <?php if ($isStaff): ?>


        <!-- Staff sidebar links -->
        <?php if ($isAdmin): ?>
            <li class="<?= $itemClass ?>">
                <?= $this->Html->link($linkLabel('Users', 'bi-people'), ['controller' => 'Users', 'action' => 'index'], ['class' => $linkClass . ($currentController === 'Users' && $currentAction === 'index' ? ' active' : ''), 'escape' => false]) ?>
            </li>
        <?php endif; ?>

        <?php if ($isAdmin || $isClinician): ?>
            <li class="<?= $itemClass ?>">
                <?= $this->Html->link($linkLabel('Patients', 'bi-person-check'), ['controller' => 'Patients', 'action' => 'index'], ['class' => $linkClass . ($currentController === 'Patients' ? ' active' : ''), 'escape' => false]) ?>
            </li>

            <li class="<?= $itemClass ?>">
                <?= $this->Html->link($linkLabel('Appointments', 'bi-calendar3'), ['controller' => 'Appointments', 'action' => 'index'], ['class' => $linkClass . ($currentController === 'Appointments' ? ' active' : ''), 'escape' => false]) ?>
            </li>

            <li class="<?= $itemClass ?>">
                <?= $this->Html->link($linkLabel('Health Records', 'bi-heart-pulse'), ['controller' => 'VitalData', 'action' => 'index'], ['class' => $linkClass . ($currentController === 'VitalData' ? ' active' : ''), 'escape' => false]) ?>
            </li>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
            <li class="<?= $itemClass ?>">
                <?= $this->Html->link($linkLabel('Edit Site Content', 'bi-heart-pulse'), ['plugin' => false, 'controller' => 'ContentBlocks', 'action' => 'index'], ['class' => $linkClass . ($currentController === 'ContentBlocks' ? ' active' : ''), 'escape' => false]) ?>
            </li>
        <?php endif; ?>

    <?php endif; ?>

    <?php if ($isPatient): ?>


        <!-- Patient sidebar links -->
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link($linkLabel('Appointments', 'bi-calendar3'), ['controller' => 'Appointments', 'action' => 'upcomingAppointments'], ['class' => $linkClass . ($currentController === 'Appointments' && $currentAction === 'upcomingAppointments' ? ' active' : ''), 'escape' => false]) ?>
        </li>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link($linkLabel('Health Records', 'bi-heart-pulse'), ['controller' => 'VitalData', 'action' => 'patientHistory'], ['class' => $linkClass . ($currentController === 'VitalData' && $currentAction === 'patientHistory' ? ' active' : ''), 'escape' => false]) ?>
        </li>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link($linkLabel('Reminders', 'bi-bell'), ['controller' => 'Reminders', 'action' => 'index'], ['class' => $linkClass . ($currentController === 'Reminders' ? ' active' : ''), 'escape' => false]) ?>
            <span id="reminder-badge" class="badge"></span>
        </li>

        <li class="<?= $itemClass ?>">
            <?= $this->Html->link($linkLabel('Profile', 'bi-person'), ['controller' => 'Users', 'action' => 'edit', $identity ? $identity->getIdentifier() : null], ['class' => $linkClass . ($currentController === 'Users' && $currentAction === 'edit' ? ' active' : ''), 'escape' => false]) ?>
        </li>
    <?php endif; ?>
<?php endif; ?>

<!-- ====================================================================
     AUTHENTICATION LINK (Login/Logout)
     Always visible in topbar on both desktop and mobile
     Shows "Logout" for logged-in users, "Login" for guests
     ==================================================================== -->
<?php if ($context === 'topbar' || $context === 'mobile'): ?>
    <!-- Login or logout link -->
    <li class="<?= $itemClass ?> topbar-always-show">
        <?php if ($isLoggedIn): ?>
            <?= $this->Html->link(
                $linkLabel('Logout', 'bi-box-arrow-right', $context === 'mobile'),
                ['controller' => 'Users', 'action' => 'logout'],
                ['class' => $linkClass . ' auth-button' . ($currentController === 'Users' && $currentAction === 'logout' ? ' active' : ''), 'escape' => false]
            ) ?>
        <?php else: ?>
            <?= $this->Html->link(
                $linkLabel('Login', 'bi-box-arrow-in-right', $context === 'mobile'),
                ['controller' => 'Users', 'action' => 'login'],
                ['class' => $linkClass . ' auth-button' . ($currentController === 'Users' && $currentAction === 'login' ? ' active' : ''), 'escape' => false]
            )?>
        <?php endif; ?>
    </li>
<?php endif; ?>
