<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 shadow-sm w-100">
    <div class="container-fluid px-4">
        <?= $this->Html->link(
            '<img src="/img/logo.png" alt="RespiraCare Logo" style="height:50px; width:auto;" class="align-middle me-2">' .
            '<span class="d-flex flex-column align-items-start">' .
            '<span class="navbar-logo-top">Respira</span>' .
            '<span class="navbar-logo-bottom">Care</span>' .
            '</span>',
            ['controller' => 'Pages', 'action' => 'display', 'home'],
            ['class' => 'navbar-brand d-flex align-items-center', 'escape' => false]
        ) ?>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 fw-semibold">
                <?php
                $currentController = $this->request->getParam('controller');
                $currentAction = $this->request->getParam('action');
                $currentPage = $this->request->getParam('pass.0'); // for PagesController 'display'
                ?>

                <li class="nav-item">
                    <?= $this->Html->link(
                        'Home',
                        ['controller' => 'Pages', 'action' => 'display', 'home'],
                        ['class' => 'nav-link' . ($currentController === 'Pages' && $currentPage === 'home' ? ' active' : '')]
                    ) ?>
                </li>

<!--                <li class="nav-item">-->
<!--                    --><?php //= $this->Html->link(
//                        'About',
//                        ['controller' => 'Pages', 'action' => 'display', 'about'],
//                        ['class' => 'nav-link' . ($currentController === 'Pages' && $currentPage === 'about' ? ' active' : '')]
//                    ) ?>
<!--                </li>-->

                <li class="nav-item">
                    <?= $this->Html->link(
                        'Contact',
                        ['controller' => 'ContactMessages', 'action' => 'add'],
                        ['class' => 'nav-link' . ($currentController === 'ContactMessages' && $currentAction === 'add' ? ' active' : '')]
                    ) ?>
                </li>

                <?php if ($this->Identity->isLoggedIn()): ?>
                    <?php $type= $this->Identity->get('type'); ?>

                    <?php if (in_array($type, ['admin', 'clinician'], true)): ?>
                        <li class="nav-item">
                            <?= $this->Html->link(
                                'Dashboard',
                                ['controller' => 'Dashboard', 'action' => 'index'],
                                [
                                    'class' => 'nav-link' . ($currentController === 'Dashboard' ? ' active' : '')
                                ]
                            ) ?>
                        </li>
                    <?php elseif ($type=== 'patient'): ?>
                        <li class="nav-item">
                            <?= $this->Html->link(
                                'My Profile',
                                ['controller' => 'Users', 'action' => 'view', $this->Identity->get('id')],
                                ['class' => 'nav-link' . ($currentController === 'Users' && $currentAction === 'view' ? ' active' : '')]
                            ) ?>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <li class="nav-item">
                    <?php if ($this->Identity->isLoggedIn()): ?>
                        <?= $this->Html->link(
                            'Logout',
                            ['controller' => 'Users', 'action' => 'logout'],
                            ['class' => 'nav-link' . ($currentController === 'Users' && $currentAction === 'logout' ? ' active' : '')]
                        ) ?>
                    <?php else: ?>
                        <?= $this->Html->link(
                            'Login',
                            ['controller' => 'Users', 'action' => 'login'],
                            ['class' => 'nav-link' . ($currentController === 'Users' && $currentAction === 'login' ? ' active' : '')]
                        ) ?>
                    <?php endif; ?>
                </li>


            </ul>
        </div>
    </div>
</nav><?php
