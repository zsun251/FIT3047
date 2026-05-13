<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?= h($this->Url->image($this->ContentBlock->imagePath('global-logo'))) ?>">

    <title><?= $this->fetch('title') ?> - <?= $this->ContentBlock->text('global-site-name') ?></title>

    <?= $this->fetch('meta') ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Actor&family=Agbalumo&display=swap" rel="stylesheet">

    <?= $this->Html->css([
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css',
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css',
        'styles',
        'app',
        'topbar',
        'sidebar'
    ]) ?>

    <?= $this->fetch('datatables_css') ?>
    <?= $this->fetch('css') ?>
</head>
<body class="d-flex flex-column min-vh-100">
<?= $this->element('header') ?>

<div class="app-shell flex-grow-1">
    <?= $this->element('sidebar') ?>

    <main id="main-content" class="p-4 min-w-0">
        <div class="container-fluid px-3 px-md-4 px-xl-5">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>
</div>

<?= $this->element('footer') ?>

<?= $this->Html->script([
    'https://code.jquery.com/jquery-3.7.1.min.js',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js',
    'scripts'
]) ?>

<?= $this->fetch('datatables_js') ?>
<?= $this->fetch('script') ?>
</body>
</html>
