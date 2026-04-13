<?php
/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
    <i class="bi bi-info-circle-fill me-2"></i>
    <?= $message ?>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
