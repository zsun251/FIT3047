<?php
$this->assign('title','Dashboard');

// CSS
$this->start('datatables_css');
echo $this->Html->css([
    'dashboard'
]);
$this->end();

// JS
$this->start('datatables_js');
echo $this->Html->script([
]);
$this->end();

$user = $this->Identity->get();
?>

    <div class="dash-header">
        <h1 class="dash-title">
            Welcome, <em><?= h($user->first_name . ' ' . $user->last_name)?></em> Patient
        </h1>
    </div>





