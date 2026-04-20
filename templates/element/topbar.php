<?php
?>
    <nav id="topbar" class="navbar navbar-expand-md navbar-light bg-white py-3 shadow-sm w-100">
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
                <?= $this->element('nav_links', ['context' => 'topbar']) ?>
            </ul>

        </div>
    </div>
</nav><?php
