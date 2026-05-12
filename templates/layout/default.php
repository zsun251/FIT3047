<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/img/logo.png">

    <title><?= $this->fetch('title') ?> - RespiraCare</title>

    <?= $this->fetch('meta') ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Actor&family=Agbalumo&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&display=swap" rel="stylesheet">

    <?= $this->Html->css([
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css',
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css',
        'styles',
        'topbar'
    ]) ?>

    <?= $this->fetch('css') ?>
</head>
<body class="d-flex flex-column min-vh-100">
<?= $this->element('header') ?>

<main class="flex-grow-1">
    <?= $this->Flash->render() ?>
    <?= $this->fetch('content') ?>
</main>

<?= $this->element('footer') ?>

<?= $this->Html->script([
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js',
    'scripts'
]) ?>

<?= $this->fetch('script') ?>
</body>
</html>
