<nav id="sidebar" class="sidebar" aria-label="Management navigation">

    <?php
    use App\Policy\UserAccess;

    $currentController = $this->request->getParam('controller');
    $currentAction = $this->request->getParam('action');

    $identity = $this->request->getAttribute('identity');
    $isLoggedIn = $this->Identity->isLoggedIn();
    $isStaff = $identity && UserAccess::isStaff($identity);
    $isPatient = $identity && UserAccess::isPatient($identity);

    // Helper for consistent sidebar link classes
    $linkClass = function (string $controller, ?string $action = null, ?string $customClass = null) use ($currentController, $currentAction): string {
        $isActive = $currentController === $controller && ($action === null || $currentAction === $action);

        return 'sidebar-link' . ($isActive ? ' active' : '') . ($customClass ? ' ' . $customClass : '');
    }; ?>

    <?php if ($isLoggedIn && $identity): ?>

        <?php if ($isStaff): ?>

            <!-- Staff Navigation -->
            <div class="sidebar-section-title">Overview</div>

            <?= $this->Html->link('Dashboard', ['controller' => 'Dashboard', 'action' => 'staff'], ['class' => $linkClass('Dashboard', 'staff')]) ?>

            <!-- Staff Management -->
            <div class="sidebar-section-title">Management</div>

            <?= $this->Html->link('Users', ['controller' => 'Users', 'action' => 'index'], ['class' => $linkClass('Users')]) ?>
            <?= $this->Html->link('Admins', ['controller' => 'Admins', 'action' => 'index'], ['class' => $linkClass('Admins')]) ?>
            <?= $this->Html->link('Clinicians', ['controller' => 'Clinicians', 'action' => 'index'], ['class' => $linkClass('Clinicians')]) ?>
            <?= $this->Html->link('Patients', ['controller' => 'Patients', 'action' => 'index'], ['class' => $linkClass('Patients')]) ?>

            <!-- Staff Clinical Data -->
            <div class="sidebar-section-title">Clinical Data</div>

            <?= $this->Html->link('Appointments', ['controller' => 'Appointments', 'action' => 'index'], ['class' => $linkClass('Appointments')]) ?>
            <?= $this->Html->link('Vital Data', ['controller' => 'VitalData', 'action' => 'index'], ['class' => $linkClass('VitalData')]) ?>
            <?= $this->Html->link('Symptoms', ['controller' => 'Symptoms', 'action' => 'index'], ['class' => $linkClass('Symptoms')]) ?>

            <!-- Staff Communication -->
            <div class="sidebar-section-title">Communication</div>

            <?= $this->Html->link('Contact Messages', ['controller' => 'ContactMessages', 'action' => 'index'], ['class' => $linkClass('ContactMessages')]) ?>

            <!-- Staff Resources -->
            <div class="sidebar-section-title">Resources</div>

            <?= $this->Html->link('Resources', ['controller' => 'Resources', 'action' => 'index'], ['class' => $linkClass('Resources')]) ?>

        <?php elseif ($isPatient): ?>

            <!-- Patient Navigation -->
            <div class="sidebar-section-title">Overview</div>

            <?= $this->Html->link('Dashboard', ['controller' => 'Dashboard', 'action' => 'patient'], ['class' => $linkClass('Dashboard', 'patient')]) ?>

            <!-- Patient Health Links -->
            <div class="sidebar-section-title">Health Information</div>

            <?= $this->Html->link('Historical Health Data', ['controller' => 'VitalData', 'action' => 'index'], ['class' => $linkClass('VitalData', 'index')]) ?>
            <?= $this->Html->link('Appointments', ['controller' => 'Appointments', 'action' => 'index'], ['class' => $linkClass('Appointments')]) ?>
            <?= $this->Html->link('Resources', ['controller' => 'Resources', 'action' => 'browse'], ['class' => $linkClass('Resources', 'browse')]) ?>

            <!-- Patient Settings -->
            <div class="sidebar-section-title">Settings</div>

            <?= $this->Html->link('Profile', ['controller' => 'Users', 'action' => 'edit', $identity->getIdentifier()], ['class' => $linkClass('Users', 'edit')]) ?>

        <?php endif; ?>

    <?php endif; ?>

</nav>
