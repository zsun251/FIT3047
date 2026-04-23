<?php
/**
 * src/templates/layout/app.php
 *
 * Wraps all app system pages.
 *
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Should create .ico files of different sizes -->
    <link rel="icon" type="image/x-icon" href="/img/logo.png">

    <title><?= $this->fetch('title') ?> - RespiraCare</title>

    <?= $this->fetch('meta') ?>

    <!-- Fonts (inherited from your existing setup) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Actor&family=Agbalumo&display=swap" rel="stylesheet">

    <!-- Shared CSS: Bootstrap + icons + global site styles + app styles -->
    <?= $this->Html->css([
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css',
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css',
        'styles',
        'app',
        'topbar',
        'sidebar'
    ]) ?>

    <!-- DataTables CSS -->
    <?= $this->fetch('datatables_css') ?>

    <!-- Per-page CSS -->
    <?= $this->fetch('css') ?>
</head>
<body class="d-flex flex-column min-vh-100">

<?= $this->element('header') ?>

<div class="app-shell d-flex-grow-1">

    <aside id="sidebar" class="flex-shrink-0">
        <?= $this->element('sidebar') ?>
    </aside>

    <main id="main-content" class="flex-grow-1 p-4 min-w-0">
        <div class="container-fluid px-3 px-md-4 px-xl-5">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>

</div>

<?= $this->element('footer') ?>

<!-- Shared JS: Bootstrap + global scripts -->
<?= $this->Html->script([
    'https://code.jquery.com/jquery-3.7.1.min.js',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js',
    'scripts',
]) ?>

<!-- DataTables JS -->
<?= $this->fetch('datatables_js') ?>

<!-- Per-page JS -->
<?= $this->fetch('script') ?>

</body>
</html>
