<?php
use App\Policy\UserAccess;

// ============================================================================
// SETUP: Get current request information and user identity
// ============================================================================
$identity = $this->request->getAttribute('identity');
$currentController = $this->request->getParam('controller');
$currentAction = $this->request->getParam('action');
$currentPage = $this->request->getParam('pass.0'); // For PagesController 'display' action
$isLoggedIn = $this->Identity->isLoggedIn();
$isStaff = $identity && UserAccess::isStaff($identity);
$isPatient = $identity && UserAccess::isPatient($identity);

// ============================================================================
// CONTEXT CONFIGURATION: Determine styling based on where this is rendered
// ============================================================================
// $context can be 'topbar' or 'sidebar' - passed when including this element
// This determines which CSS classes to use and whether to show section titles
$linkClass = ($context ?? 'topbar') === 'topbar' ? 'nav-link' : 'sidebar-link';
$itemClass = ($context ?? 'topbar') === 'topbar' ? 'nav-item' : '';
// Show sections in sidebar always, and in topbar only on mobile
$showSections = ($context ?? 'topbar') === 'sidebar' || ($context === 'topbar');?>

<?php if ($context === 'topbar'): ?>
    <!-- ====================================================================
         PUBLIC NAVIGATION LINKS (Desktop & Mobile Topbar)
         These links ALWAYS show in the topbar on both desktop and mobile
         ==================================================================== -->
    <li class="<?= $itemClass ?> topbar-always-show">
        <?= $this->Html->link('Home',
            ['controller' => 'Pages', 'action' => 'display', 'home'],
            ['class' => $linkClass . ($currentController === 'Pages' && $currentPage === 'home' ? ' active' : '')]
        ) ?>
    </li>
    <li class="<?= $itemClass ?> topbar-always-show">
        <?= $this->Html->link('Contact',
            ['controller' => 'ContactMessages', 'action' => 'add'],
            ['class' => $linkClass . ($currentController === 'ContactMessages' && $currentAction === 'add' ? ' active' : '')]
        ) ?>
    </li>
<?php endif; ?>

<?php if ($isLoggedIn): ?>
    <!-- ====================================================================
         AUTHENTICATED USER NAVIGATION
         This section only appears for logged-in users
         ==================================================================== -->

    <?php if ($showSections): ?><div class="sidebar-section-title">Overview</div><?php endif; ?>

    <!-- ====================================================================
         DASHBOARD LINKS (Role-specific)
         Staff and Patient users see different dashboard links
         Desktop: Always visible in topbar
         Mobile: Visible in topbar hamburger menu
         ==================================================================== -->
    <?php if ($isStaff): ?>
        <li class="<?= $itemClass ?><?= $context === 'topbar' ? ' topbar-always-show' : '' ?>">
            <?= $this->Html->link('Dashboard',
                ['controller' => 'Dashboard', 'action' => 'staff'],
                ['class' => $linkClass . ($currentController === 'Dashboard' && $currentAction === 'staff' ? ' active' : '')]
            ) ?>
        </li>
    <?php elseif ($isPatient): ?>
        <li class="<?= $itemClass ?><?= $context === 'topbar' ? ' topbar-always-show' : '' ?>">
            <?= $this->Html->link('Dashboard',
                ['controller' => 'Dashboard', 'action' => 'patient'],
                ['class' => $linkClass . ($currentController === 'Dashboard' && $currentAction === 'patient' ? ' active' : '')]
            ) ?>
        </li>
    <?php endif; ?>

    <?php if ($isStaff): ?>
        <!-- ================================================================
             STAFF-ONLY NAVIGATION SECTIONS
             These links are for staff members to manage the system
             Desktop: Hidden in topbar (sidebar handles these)
             Mobile: Visible in topbar hamburger menu (no sidebar on mobile)
             ================================================================ -->

        <!-- MANAGEMENT SECTION: User management links -->
        <?php if ($showSections): ?><div class="sidebar-section-title">Management</div><?php endif; ?>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link('Users', ['controller' => 'Users', 'action' => 'index'],
                ['class' => $linkClass . ($currentController === 'Users' ? ' active' : '')]) ?>
        </li>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link('Admins', ['controller' => 'Admins', 'action' => 'index'],
                ['class' => $linkClass . ($currentController === 'Admins' ? ' active' : '')]) ?>
        </li>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link('Clinicians', ['controller' => 'Clinicians', 'action' => 'index'],
                ['class' => $linkClass . ($currentController === 'Clinicians' ? ' active' : '')]) ?>
        </li>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link('Patients', ['controller' => 'Patients', 'action' => 'index'],
                ['class' => $linkClass . ($currentController === 'Patients' ? ' active' : '')]) ?>
        </li>

        <!-- CLINICAL DATA SECTION: Medical data management -->
        <?php if ($showSections): ?><div class="sidebar-section-title">Clinical Data</div><?php endif; ?>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link('Appointments', ['controller' => 'Appointments', 'action' => 'index'],
                ['class' => $linkClass . ($currentController === 'Appointments' ? ' active' : '')]) ?>
        </li>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link('Vital Data', ['controller' => 'VitalData', 'action' => 'index'],
                ['class' => $linkClass . ($currentController === 'VitalData' ? ' active' : '')]) ?>
        </li>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link('Symptoms', ['controller' => 'Symptoms', 'action' => 'index'],
                ['class' => $linkClass . ($currentController === 'Symptoms' ? ' active' : '')]) ?>
        </li>

        <!-- COMMUNICATION SECTION: Contact message management -->
        <?php if ($showSections): ?><div class="sidebar-section-title">Communication</div><?php endif; ?>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link('Contact Messages', ['controller' => 'ContactMessages', 'action' => 'index'],
                ['class' => $linkClass . ($currentController === 'ContactMessages' ? ' active' : '')]) ?>
        </li>

        <!-- RESOURCES SECTION: Educational resources management -->
        <?php if ($showSections): ?><div class="sidebar-section-title">Resources</div><?php endif; ?>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link('Resources', ['controller' => 'Resources', 'action' => 'index'],
                ['class' => $linkClass . ($currentController === 'Resources' ? ' active' : '')]) ?>
        </li>
    <?php endif; ?>

    <?php if ($isPatient): ?>
        <!-- ================================================================
             PATIENT-ONLY NAVIGATION SECTIONS
             These links allow patients to view their own health information
             Desktop: Hidden in topbar (sidebar handles these)
             Mobile: Visible in topbar hamburger menu (no sidebar on mobile)
             ================================================================ -->

        <!-- MY HEALTH INFORMATION SECTION: Personal health data access -->
        <?php if ($showSections): ?><div class="sidebar-section-title">My Health Information</div><?php endif; ?>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link('Appointments', ['controller' => 'Appointments', 'action' => 'index'],
                ['class' => $linkClass . ($currentController === 'Appointments' ? ' active' : '')]) ?>
        </li>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link('Health Data', ['controller' => 'VitalData', 'action' => 'index'],
                ['class' => $linkClass . ($currentController === 'VitalData' ? ' active' : '')]) ?>
        </li>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link('Resources', ['controller' => 'Resources', 'action' => 'browse'],
                ['class' => $linkClass . ($currentController === 'Resources' ? ' active' : '')]) ?>
        </li>

        <!-- SETTINGS SECTION: Personal account settings -->
        <?php if ($showSections): ?><div class="sidebar-section-title">Settings</div><?php endif; ?>
        <li class="<?= $itemClass ?>">
            <?= $this->Html->link('Profile', ['controller' => 'Users', 'action' => 'edit', $identity->getIdentifier()],
                ['class' => $linkClass . ($currentController === 'Users' && $currentAction === 'edit' ? ' active' : '')]) ?>
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
            <?= $this->Html->link('Logout',
                ['controller' => 'Users', 'action' => 'logout'],
                ['class' => $linkClass . ($currentController === 'Users' && $currentAction === 'logout' ? ' active' : '')]
            ) ?>
        <?php else: ?>
            <?= $this->Html->link('Login',
                ['controller' => 'Users', 'action' => 'login'],
                ['class' => $linkClass . ($currentController === 'Users' && $currentAction === 'login' ? ' active' : '')]
            ) ?>
        <?php endif; ?>
    </li>
<?php endif; ?>
