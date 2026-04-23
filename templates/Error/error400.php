<?php
/**
 * @var \App\View\AppView $this
 * @var string $message
 * @var string $url
 */
use Cake\Core\Configure;

$this->setLayout('error');
$this->assign('title', 'Page Not Found');

if (Configure::read('debug')) :
    $this->setLayout('dev_error');
    $this->assign('templateName', 'error400.php');
    $this->start('file');
    echo $this->element('auto_table_warning');
    $this->end();
endif;
?>

<div class="error-container">
    <div class="error-icon">
        <i class="bi bi-compass text-muted"></i>
    </div>

    <div class="error-code">404</div>

    <h1 class="error-title">Page Not Found</h1>

    <p class="error-message">
        Sorry, we couldn't find the page you're looking for. The page might have been moved or deleted.
    </p>

    <div class="error-buttons">
        <?= $this->Html->link(
            '<i class="bi bi-house-door me-2"></i>Go to Homepage',
            '/',
            ['class' => 'btn btn-primary', 'escape' => false]
        ) ?>

        <a href="javascript:history.back()" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Go Back
        </a>
    </div>
</div>
