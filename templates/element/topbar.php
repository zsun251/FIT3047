<nav id="topbar" class="navbar navbar-expand-md navbar-dark py-3 w-100">
    <div class="container-fluid px-4">
        <?= $this->Html->link(
            $this->ContentBlock->image('global-logo', [
                'alt' => $this->ContentBlock->text('global-logo-alt'),
                'class' => 'navbar-logo'
            ]) . '<span class="navbar-wordmark">' . $this->ContentBlock->text('global-site-name') . '</span>',
            ['controller' => 'Pages', 'action' => 'display', 'home'],
            ['class' => 'navbar-brand', 'escape' => false]
        ) ?>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <!-- Desktop topbar links only -->
            <ul class="navbar-nav ms-auto mb-2 mb-md-0 fw-semibold d-none d-md-flex">
                <?= $this->element('nav_links', ['context' => 'topbar']) ?>
            </ul>

            <!-- Mobile menu replaces both the desktop topbar and sidebar links -->
            <ul class="navbar-nav ms-auto mb-2 mb-md-0 fw-semibold d-md-none">
                <?= $this->element('nav_links', ['context' => 'mobile']) ?>
            </ul>
        </div>
    </div>
</nav>
