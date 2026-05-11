<?php
use App\Policy\UserAccess;

// ============================================================================
// SETUP: Get current request information and user identity
// ============================================================================
$identity = $this->request->getAttribute('identity');
$currentController = $this->request->getParam('controller');
$currentAction = $this->request->getParam('action');
$currentPage = $this->request->getParam('pass.0'); // For PagesController 'display' action

// Determine authentication and role state from the request identity
$isLoggedIn = $identity !== null;
$isStaff = $identity && UserAccess::isStaff($identity);
$isPatient = $identity && UserAccess::isPatient($identity);
$isAdmin = $identity && UserAccess::isAdmin($identity);
$isClinician = $identity && UserAccess::isClinician($identity);

// ============================================================================
// CONTEXT CONFIGURATION: Determine styling based on where this is rendered
// ============================================================================
// $context can be 'topbar' or 'sidebar' - passed when including this element
// This determines which CSS classes to use and whether to show section titles
$context = $context ?? 'topbar';
$linkClass = $context === 'topbar' ? 'nav-link' : 'sidebar-link';
$itemClass = $context === 'topbar' ? 'nav-item' : '';

// Show the single sidebar section title only in the sidebar
$showSections = $context === 'sidebar';

// Add icons to sidebar links, and mobile topbar links
$linkLabel = function (string $label, string $icon) use ($context): string {
    if ($context === 'sidebar') {
        return '<i class="bi ' . h($icon) . '"></i><span>' . h($label) . '</span>';
    }

    return '<i class="bi ' . h($icon) . ' topbar-mobile-icon"></i><span>' . h($label) . '</span>';
};

$resourcesRoute = ['controller' => 'Resources', 'action' => 'browse'];
if ($isStaff || $isClinician || $isAdmin) {
    $resourcesRoute = ['controller' => 'Resources', 'action' => 'index'];
}

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
    <!-- ====================================================================
         PUBLIC NAVIGATION LINKS (Desktop & Mobile Topbar)
         These links ALWAYS show in the topbar on both desktop and mobile
         ==================================================================== -->
    <li class="<?= $itemClass ?> topbar-always-show">
        <?= $this->Html->link(
            $linkLabel('Home', 'bi-house'),
            ['controller' => 'Pages', 'action' => 'display', 'home'],
            [
                'class' => $linkClass . ($currentController === 'Pages' && $currentPage === 'home' ? ' active' : ''),
                'escape' => false
            ]
        ) ?>
    </li>

    <?php if (!$isLoggedIn || $isPatient): ?>
        <li class="<?= $itemClass ?> topbar-always-show">
            <?= $this->Html->link(
                $linkLabel('Contact', 'bi-envelope'),
                ['controller' => 'ContactMessages', 'action' => 'add'],
                [
                    'class' => $linkClass . ($currentController === 'ContactMessages' && $currentAction === 'add' ? ' active' : ''),
                    'escape' => false
                ]
            ) ?>
        </li>
    <?php endif; ?>

    <?php if ($isLoggedIn): ?>
        <li class="<?= $itemClass ?> topbar-always-show">
            <?= $this->Html->link(
                $linkLabel('Resources', 'bi-journal-medical'),
                $resourcesRoute,
                [
                    'class' => $linkClass . ($currentController === 'Resources' ? ' active' : ''),
                    'escape' => false
                ]
            ) ?>
        </li>
    <?php endif; ?>

    <?php if ($isLoggedIn && $dashboardRoute): ?>
        <li class="<?= $itemClass ?> topbar-always-show">
            <?= $this->Html->link(
                $linkLabel('Dashboard', 'bi-grid'),
                $dashboardRoute,
                [
                    'class' => $linkClass . ($dashboardActive ? ' active' : ''),
                    'escape' => false
                ]
            ) ?>
        </li>
    <?php endif; ?>
<?php endif; ?>

<?php if ($context === 'sidebar' && $isLoggedIn): ?>
    <!-- ====================================================================
         AUTHENTICATED USER NAVIGATION
         This section only appears for logged-in users
         ==================================================================== -->

    <?php if ($showSections): ?><div class="sidebar-section-title">Navigation</div><?php endif; ?>

    <!-- ====================================================================
         DASHBOARD LINKS (Role-specific)
         Staff and Patient users see different dashboard links
         Desktop: Always visible in topbar
         Mobile: Visible in topbar hamburger menu
         ==================================================================== -->
    <?php if ($dashboardRoute): ?>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link($linkLabel('Dashboard', 'bi-grid'), $dashboardRoute, ['class' => $linkClass . ($dashboardActive ? ' active' : ''), 'escape' => false]) ?>
        </li>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link($linkLabel('Resources', 'bi-journal-medical'), $resourcesRoute, ['class' => $linkClass . ($currentController === 'Resources' ? ' active' : ''), 'escape' => false]) ?>
        </li>
    <?php endif; ?>

    <?php if ($isStaff): ?>
        <!-- ================================================================
             STAFF-ONLY NAVIGATION LINKS
             Admins can manage users and view clinical areas.
             Clinicians can view assigned patients and clinical areas.
             ================================================================ -->

        <?php if ($isAdmin): ?>
            <li class="<?= $itemClass ?>">
                <?= $this->Html->link($linkLabel('Users', 'bi-people'), ['controller' => 'Users', 'action' => 'index'], ['class' => $linkClass . ($currentController === 'Users' && $currentAction === 'index' ? ' active' : ''), 'escape' => false]) ?>
            </li>
        <?php endif; ?>

        <?php if ($isAdmin || $isClinician): ?>
            <li class="<?= $itemClass ?>">
                <?= $this->Html->link($linkLabel('Patients', 'bi-person'), ['controller' => 'Patients', 'action' => 'index'], ['class' => $linkClass . ($currentController === 'Patients' ? ' active' : ''), 'escape' => false]) ?>
            </li>

            <li class="<?= $itemClass ?>">
                <?= $this->Html->link($linkLabel('Appointments', 'bi-calendar3'), ['controller' => 'Appointments', 'action' => 'index'], ['class' => $linkClass . ($currentController === 'Appointments' ? ' active' : ''), 'escape' => false]) ?>
            </li>

            <li class="<?= $itemClass ?>">
                <?= $this->Html->link($linkLabel('Health Records', 'bi-heart-pulse'), ['controller' => 'VitalData', 'action' => 'index'], ['class' => $linkClass . ($currentController === 'VitalData' ? ' active' : ''), 'escape' => false]) ?>
            </li>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($isPatient): ?>
        <!-- ================================================================
             PATIENT-ONLY NAVIGATION SECTIONS
             These links allow patients to view their own health information
             Desktop: Hidden in topbar (sidebar handles these)
             Mobile: Visible in topbar hamburger menu (no sidebar on mobile)
             ================================================================ -->

        <!-- MY HEALTH INFORMATION SECTION: Personal health data access -->
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link($linkLabel('Appointments', 'bi-calendar3'), ['controller' => 'Appointments', 'action' => 'upcomingAppointments'], ['class' => $linkClass . ($currentController === 'Appointments' && $currentAction === 'upcomingAppointments' ? ' active' : ''), 'escape' => false]) ?>
        </li>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link($linkLabel('Health Records', 'bi-heart-pulse'), ['controller' => 'VitalData', 'action' => 'patientHistory'], ['class' => $linkClass . ($currentController === 'VitalData' && $currentAction === 'patientHistory' ? ' active' : ''), 'escape' => false]) ?>
        </li>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link($linkLabel('Reminders', 'bi-bell'), ['controller' => 'Reminders', 'action' => 'index'],
                ['class' => $linkClass . ($currentController === 'Reminders' ? ' active' : ''), 'escape' => false]) ?>
            <span id="reminder-badge" class="badge"></span>
        </li>

        <!-- SETTINGS SECTION: Personal account settings -->
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link($linkLabel('Profile', 'bi-person'), ['controller' => 'Users', 'action' => 'edit', $identity ? $identity->getIdentifier() : null], ['class' => $linkClass . ($currentController === 'Users' && $currentAction === 'edit' ? ' active' : ''), 'escape' => false]) ?>
        </li>
    <?php endif; ?>
<?php endif; ?>

<?php if ($context === 'topbar'): ?>
    <!-- ====================================================================
         AUTHENTICATION LINK (Login/Logout)
         Always visible in topbar on both desktop and mobile
         Shows "Logout" for logged-in users, "Login" for guests
         ==================================================================== -->
    <li class="<?= $itemClass ?> topbar-always-show">
        <?php if ($isLoggedIn): ?>
            <?= $this->Html->link(
                $linkLabel('Logout', 'bi-box-arrow-right'),
                ['controller' => 'Users', 'action' => 'logout'],
                [
                    'class' => $linkClass . ($currentController === 'Users' && $currentAction === 'logout' ? ' active' : ''),
                    'escape' => false
                ]
            ) ?>
        <?php else: ?>
            <?= $this->Html->link(
                $linkLabel('Login', 'bi-box-arrow-in-right'),
                ['controller' => 'Users', 'action' => 'login'],
                [
                    'class' => $linkClass . ($currentController === 'Users' && $currentAction === 'login' ? ' active' : ''),
                    'escape' => false
                ]
            ) ?>
        <?php endif; ?>
    </li>
<?php endif; ?>
