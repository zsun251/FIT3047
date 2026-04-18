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

<?php
$displayName = trim(($currentUser->first_name ?? '') . ' ' . ($currentUser->last_name ?? ''));
?>

<div class="dash-header">
    <h1 class="dash-title">
        Welcome, <em><?= h($displayName !== '' ? $displayName : $currentUser->username) ?></em> Patient
    </h1>
</div>





