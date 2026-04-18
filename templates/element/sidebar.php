<nav id="sidebar" class="sidebar" aria-label="Management navigation">

    <?php
    use App\Policy\UserAccess;

    $currentController = $this->request->getParam('controller');
    $currentAction = $this->request->getParam('action');

    $identity = $this->request->getAttribute('identity');
    $isLoggedIn = $this->Identity->isLoggedIn();
    $isStaff = $identity && UserAccess::isStaff($identity);
    $isPatient = $identity && UserAccess::isPatient($identity);
    ?>

    <div class="sidebar-section-title">Overview</div>

    <?php if ($this->Identity->isLoggedIn() && $identity): ?>

        <?php if (UserAccess::isStaff($identity)): ?>

            <!-- STAFF DASHBOARD -->
            <?= $this->Html->link(
                'Dashboard',
                ['controller' => 'Dashboard', 'action' => 'staff'],
                ['class' => 'sidebar-link' . ($currentController === 'Dashboard' && $currentAction === 'staff' ? ' active' : '')]
            ) ?>

        <?php elseif (UserAccess::isPatient($identity)): ?>

            <!-- PATIENT DASHBOARD ONLY -->
            <?= $this->Html->link(
                'Dashboard',
                ['controller' => 'Dashboard', 'action' => 'patient'],
                ['class' => 'sidebar-link' . ($currentController === 'Dashboard' && $currentAction === 'patient' ? ' active' : '')]
            ) ?>

        <?php endif; ?>

    <?php endif; ?>


    <?php if (UserAccess::isStaff($identity)): ?>

        <div class="sidebar-section-title">Management</div>

        <?= $this->Html->link('Users', ['controller' => 'Users', 'action' => 'index'], ['class' => 'sidebar-link' . ($currentController === 'Users' ? ' active' : '')]) ?>
        <?= $this->Html->link('Admins', ['controller' => 'Admins', 'action' => 'index'], ['class' => 'sidebar-link' . ($currentController === 'Admins' ? ' active' : '')]) ?>
        <?= $this->Html->link('Clinicians', ['controller' => 'Clinicians', 'action' => 'index'], ['class' => 'sidebar-link' . ($currentController === 'Clinicians' ? ' active' : '')]) ?>
        <?= $this->Html->link('Patients', ['controller' => 'Patients', 'action' => 'index'], ['class' => 'sidebar-link' . ($currentController === 'Patients' ? ' active' : '')]) ?>

        <div class="sidebar-section-title">Clinical Data</div>

        <?= $this->Html->link('Appointments', ['controller' => 'Appointments', 'action' => 'index'], ['class' => 'sidebar-link' . ($currentController === 'Appointments' ? ' active' : '')]) ?>
        <?= $this->Html->link('Vital Data', ['controller' => 'VitalData', 'action' => 'index'], ['class' => 'sidebar-link' . ($currentController === 'VitalData' ? ' active' : '')]) ?>
        <?= $this->Html->link('Symptoms', ['controller' => 'Symptoms', 'action' => 'index'], ['class' => 'sidebar-link' . ($currentController === 'Symptoms' ? ' active' : '')]) ?>

        <div class="sidebar-section-title">Communication</div>

        <?= $this->Html->link('Contact Messages', ['controller' => 'ContactMessages', 'action' => 'index'], ['class' => 'sidebar-link' . ($currentController === 'ContactMessages' ? ' active' : '')]) ?>

        <div class="sidebar-section-title">Resources</div>

        <?= $this->Html->link('Resources', ['controller' => 'Resources', 'action' => 'index'], ['class' => 'sidebar-link' . ($currentController === 'Resources' ? ' active' : '')]) ?>

    <?php endif; ?>


    <!-- PATIENT NAVIGATION -->
    <?php if (UserAccess::isPatient($identity)): ?>

        <!-- PATIENT HEALTH LINKS -->
        <div class="sidebar-section-title">My Heath Information</div>

        <?= $this->Html->link('Appointments', ['controller' => 'Appointments', 'action' => 'index'], ['class' => 'sidebar-link' . ($currentController === 'Appointments' ? ' active' : '')]) ?>
        <?= $this->Html->link('Health Data', ['controller' => 'VitalData', 'action' => 'add'], ['class' => 'sidebar-link' . ($currentController === 'VitalData' && $currentAction === 'add' ? ' active' : '')]) ?>

<!--    Maybe have the symptoms show on the health page as well, and have that be a historical data page,
        and then on the dashboard a button that will open the form to input the data, as that will be
        quicker for the patients-->
<!--        --><?php //= $this->Html->link('Symptoms', ['controller' => 'Symptoms', 'action' => 'index'], ['class' => 'sidebar-link' . ($currentController === 'Symptoms' ? ' active' : '')]) ?>

        <?= $this->Html->link('Resources', ['controller' => 'Resources', 'action' => 'browse'], ['class' => 'sidebar-link' . ($currentController === 'Resources' ? ' active' : '')]) ?>

        <!-- PATIENT SETTINGS -->
        <div class="sidebar-section-title">Settings</div>

        <?= $this->Html->link('Profile', ['controller' => 'Users', 'action' => 'edit', $identity->getIdentifier()], ['class' => 'sidebar-link' . ($currentController === 'Users' && $currentAction === 'edit' ? ' active' : '')]) ?>

    <?php endif; ?>



</nav>
