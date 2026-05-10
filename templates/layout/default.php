<?php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Should create .ico files of different sizes -->
    <link rel="icon" type="image/x-icon" href="/img/logo.png">

    <title><?= $this->fetch('title') ?> - RespiraCare</title>

    <!-- Per-page meta -->
    <?= $this->fetch('meta') ?>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Actor&family=Agbalumo&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&display=swap" rel="stylesheet">

    <!-- Shared CSS -->
    <!-- This line is to load CakePHP's default styles   -->
    <?= $this->Html->css([
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css',
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css',
        'styles',
        'topbar'
    ]) ?>

    <!-- Per page  -->
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Load the header  -->
    <?= $this->element('header') ?>

    <div class="d-flex">
    <main class="flex-grow-1">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
    </main>

    </div>

    <!-- Shared Footer -->
    <?= $this->element('footer') ?>


    <!-- Shared JS -->
    <?= $this->Html->script('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css') ?>

    <?= $this->Html->script('scripts') ?>
    <!-- Per-page JS -->
    <?= $this->fetch('script') ?>

</body>
</html>

