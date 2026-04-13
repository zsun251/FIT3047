<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->fetch('title') ?> | Data Management</title>

    <!-- Bootstrap 5 CSS -->
    <?= $this->Html->css('https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', ['block' => false]) ?>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>

    <style>
        /* ── Core layout ───────────────────────────────────── */
        :root {
            --sidebar-width: 260px;
            --topbar-height: 56px;
            --sidebar-bg: #1a1f2e;
            --sidebar-hover: #2a3147;
            --sidebar-active: #3b5bdb;
            --sidebar-text: #adb5bd;
            --sidebar-text-active: #ffffff;
            --sidebar-border: rgba(255,255,255,0.06);
        }

        body {
            background-color: #f4f6fb;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        /* ── Top navbar ─────────────────────────────────────── */
        #topbar {
            height: var(--topbar-height);
            background: #ffffff;
            border-bottom: 1px solid #e5e9f2;
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1040;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }

        /* ── Sidebar ────────────────────────────────────────── */
        #sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            bottom: 0;
            z-index: 1030;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform 0.3s ease, width 0.3s ease;
        }

        /* Mobile: slide sidebar off-screen by default */
        @media (max-width: 991.98px) {
            #sidebar {
                transform: translateX(-100%);
            }
            #sidebar.show {
                transform: translateX(0);
            }
            #main-content {
                margin-left: 0 !important;
            }
        }

        /* Desktop: always visible */
        @media (min-width: 992px) {
            #sidebar {
                transform: translateX(0);
            }
        }

        /* ── Sidebar nav items ──────────────────────────────── */
        .sidebar-section-title {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #4a5568;
            padding: 1.25rem 1.25rem 0.4rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1.25rem;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0;
            transition: background 0.15s, color 0.15s;
            position: relative;
        }

        .sidebar-link:hover {
            background: var(--sidebar-hover);
            color: var(--sidebar-text-active);
        }

        .sidebar-link.active {
            background: var(--sidebar-active);
            color: var(--sidebar-text-active);
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: #74c0fc;
            border-radius: 0 2px 2px 0;
        }

        .sidebar-link i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        /* Collapsible sub-menu */
        .sidebar-submenu .sidebar-link {
            padding-left: 3.25rem;
            font-size: 0.825rem;
            font-weight: 400;
        }

        /* ── Main content ───────────────────────────────────── */
        #main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 1.75rem;
            min-height: calc(100vh - var(--topbar-height));
            transition: margin-left 0.3s ease;
        }

        /* ── Backdrop (mobile overlay) ──────────────────────── */
        #sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 1025;
        }

        #sidebar-backdrop.show {
            display: block;
        }

        /* ── Sidebar toggle button (mobile) ─────────────────── */
        #sidebarToggle {
            border: none;
            background: none;
            color: #495057;
            font-size: 1.25rem;
            padding: 0.25rem 0.5rem;
            cursor: pointer;
        }

        #sidebarToggle:hover {
            color: #212529;
        }

        /* ── Scrollbar styling for sidebar ─────────────────── */
        #sidebar::-webkit-scrollbar { width: 4px; }
        #sidebar::-webkit-scrollbar-track { background: transparent; }
        #sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }
    </style>
</head>
<body>

<!-- ═══════════════════════════════════════
     TOP NAVBAR
════════════════════════════════════════ -->
<nav id="topbar" class="d-flex align-items-center px-3 gap-3">
    <!-- Sidebar toggle (visible on mobile) -->
    <button id="sidebarToggle" class="d-lg-none" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>

    <!-- Brand -->
    <a href="/" class="text-decoration-none d-flex align-items-center gap-2">
        <!-- Replace with your $this->Html->image() logo if needed -->
        <div style="width:28px;height:28px;background:#3b5bdb;border-radius:6px;"></div>
        <span class="fw-bold text-dark" style="font-size:1rem;">YourApp</span>
    </a>

    <span class="text-muted mx-1" style="font-size:1.2rem;">›</span>
    <span class="text-muted" style="font-size:0.875rem;">Data Management</span>

    <!-- Right side: your main site nav links -->
    <div class="ms-auto d-flex align-items-center gap-3">
        <?= $this->Html->link('Home', '/', ['class' => 'text-muted text-decoration-none small']) ?>
        <?= $this->Html->link('Dashboard', ['controller' => 'Pages', 'action' => 'display', 'dashboard'], ['class' => 'text-muted text-decoration-none small']) ?>

        <!-- User dropdown -->
        <div class="dropdown">
            <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
                <span class="d-none d-sm-inline small">Account</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><?= $this->Html->link('<i class="bi bi-box-arrow-right me-2"></i>Logout', ['controller' => 'Users', 'action' => 'logout'], ['class' => 'dropdown-item', 'escape' => false]) ?></li>
            </ul>
        </div>
    </div>
</nav>

<!-- ═══════════════════════════════════════
     SIDEBAR BACKDROP (mobile overlay)
════════════════════════════════════════ -->
<div id="sidebar-backdrop"></div>

<!-- ═══════════════════════════════════════
     SIDEBAR
════════════════════════════════════════ -->


// Also conditionally apply the margin:
<main id="main-content"
      style="<?= $this->get('showSidebar', true) ? '' : 'margin-left:0' ?>">
    <?php if ($this->get('showSidebar', true)): ?>
<nav id="sidebar" aria-label="Data management navigation">

    <!-- ── Section: Overview ── -->
    <div class="sidebar-section-title">Overview</div>
    <?= $this->Html->link(
        '<i class="bi bi-speedometer2"></i> Dashboard',
        ['controller' => 'Admin', 'action' => 'index'],
        ['class' => 'sidebar-link' . ($this->request->getParam('controller') === 'Admin' ? ' active' : ''), 'escape' => false]
    ) ?>

    <!-- ── Section: Data ── -->
    <div class="sidebar-section-title">Data</div>

    <?= $this->Html->link(
        '<i class="bi bi-people"></i> Users',
        ['controller' => 'Users', 'action' => 'index'],
        ['class' => 'sidebar-link' . ($this->request->getParam('controller') === 'Users' ? ' active' : ''), 'escape' => false]
    ) ?>

    <?= $this->Html->link(
        '<i class="bi bi-box-seam"></i> Products',
        ['controller' => 'Products', 'action' => 'index'],
        ['class' => 'sidebar-link' . ($this->request->getParam('controller') === 'Products' ? ' active' : ''), 'escape' => false]
    ) ?>

    <!-- Example: collapsible sub-menu -->
    <a class="sidebar-link" data-bs-toggle="collapse" href="#reportsMenu" role="button"
       aria-expanded="false" aria-controls="reportsMenu">
        <i class="bi bi-bar-chart"></i>
        Reports
        <i class="bi bi-chevron-down ms-auto" style="font-size:0.75rem;"></i>
    </a>
    <div class="collapse sidebar-submenu" id="reportsMenu">
        <?= $this->Html->link('<i class="bi bi-dot"></i> Sales', ['controller' => 'Reports', 'action' => 'sales'], ['class' => 'sidebar-link', 'escape' => false]) ?>
        <?= $this->Html->link('<i class="bi bi-dot"></i> Traffic', ['controller' => 'Reports', 'action' => 'traffic'], ['class' => 'sidebar-link', 'escape' => false]) ?>
    </div>

    <!-- ── Section: System ── -->
    <div class="sidebar-section-title">System</div>

    <?= $this->Html->link(
        '<i class="bi bi-sliders"></i> Settings',
        ['controller' => 'Settings', 'action' => 'index'],
        ['class' => 'sidebar-link' . ($this->request->getParam('controller') === 'Settings' ? ' active' : ''), 'escape' => false]
    ) ?>

    <?= $this->Html->link(
        '<i class="bi bi-clock-history"></i> Logs',
        ['controller' => 'Logs', 'action' => 'index'],
        ['class' => 'sidebar-link', 'escape' => false]
    ) ?>

    <!-- Spacer at bottom -->
    <div style="height: 2rem;"></div>
</nav>
    <?php endif; ?>

<!-- ═══════════════════════════════════════
     MAIN CONTENT
════════════════════════════════════════ -->
<main id="main-content">

    <!-- Page header (optional — views can override via fetch) -->
    <?php if ($this->fetch('page-header')): ?>
        <div class="mb-4">
            <?= $this->fetch('page-header') ?>
        </div>
    <?php endif; ?>

    <!-- Flash messages -->
    <?= $this->Flash->render() ?>

    <!-- View content -->
    <?= $this->fetch('content') ?>

</main>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ── Mobile sidebar toggle ────────────────────────────
    const sidebar    = document.getElementById('sidebar');
    const backdrop   = document.getElementById('sidebar-backdrop');
    const toggleBtn  = document.getElementById('sidebarToggle');

    function openSidebar() {
        sidebar.classList.add('show');
        backdrop.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('show');
        backdrop.classList.remove('show');
        document.body.style.overflow = '';
    }

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
    });

    backdrop.addEventListener('click', closeSidebar);

    // Close sidebar on resize to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) closeSidebar();
    });

    // ── Keep submenu open if a child is active ───────────
    document.querySelectorAll('.sidebar-submenu .sidebar-link.active').forEach(link => {
        const menu = link.closest('.collapse');
        if (menu) new bootstrap.Collapse(menu, { toggle: false }).show();
    });
</script>

<?= $this->fetch('script') ?>
</body>
</html>
