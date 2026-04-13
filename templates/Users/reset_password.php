<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg">
                <div class="card-header bg-white text-dark text-center border-bottom">
                    <h1 class="h3 mb-0">Reset Password</h1>
                </div>
                <div class="card-body p-4">

                    <?= $this->Flash->render() ?>

                    <p class="text-muted">
                        This page is present for system completeness. Password reset is not implemented in this version.
                    </p>

                    <?= $this->Form->create() ?>

                    <div class="mb-3">
                        <?= $this->Form->label('password', 'New Password', ['class' => 'form-label']) ?>
                        <?= $this->Form->control('password', [
                            'label' => false,
                            'class' => 'form-control',
                            'required' => true
                        ]) ?>
                    </div>

                    <div class="mb-3">
                        <?= $this->Form->label('confirm_password', 'Confirm Password', ['class' => 'form-label']) ?>
                        <?= $this->Form->control('confirm_password', [
                            'type' => 'password',
                            'label' => false,
                            'class' => 'form-control',
                            'required' => true
                        ]) ?>
                    </div>

                    <div class="d-grid mb-3">
                        <?= $this->Form->button('Reset Password', ['class' => 'btn btn-dark']) ?>
                    </div>

                    <div class="text-center">
                        <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'login']) ?>">Back to Login</a>
                    </div>

                    <?= $this->Form->end() ?>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
