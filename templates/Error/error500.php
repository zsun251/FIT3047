<?php
/**
 * @var \App\View\AppView $this
 * @var string $message
 * @var \Throwable $error
 */
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->setLayout('error');
$this->assign('title', 'Server Error');

if (Configure::read('debug')) :
    $this->setLayout('dev_error');
    $this->assign('title', $message);
    $this->assign('templateName', 'error500.php');
    $this->start('file');
    if (isset($error) && $error instanceof Error) :
        $file = $error->getFile();
        $line = $error->getLine();
        echo '<strong>Error in: </strong>';
        echo $this->Html->link(
            sprintf('%s, line %s', Debugger::trimPath($file), $line),
            Debugger::editorUrl($file, $line)
        );
    endif;
    echo $this->element('auto_table_warning');
    $this->end();
endif;
?>

<div class="error-container">
    <div class="error-icon">
        <i class="bi bi-exclamation-triangle text-warning"></i>
    </div>

    <div class="error-code">500</div>

    <h1 class="error-title">Oops! Something Went Wrong</h1>

    <p class="error-message">
        We're sorry, but something unexpected happened on our end.
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
