<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Account</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center justify-content-center py-5" style="min-height: 100vh;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg">
                <div class="card-header bg-white text-dark text-center border-bottom">
                    <h1 class="h3 mb-0">Create Patient Account</h1>
                </div>
                <div class="card-body p-4">

                    <?= $this->Flash->render() ?>

                    <?= $this->Form->create($user) ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $this->Form->label('first_name', 'First Name', ['class' => 'form-label']) ?>
                            <?= $this->Form->control('first_name', [
                                'label' => false,
                                'class' => 'form-control' . ($user->hasErrors() && $user->getError('first_name') ? ' is-invalid' : ''),
                                'required' => true,
                            ]) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $this->Form->label('last_name', 'Last Name', ['class' => 'form-label']) ?>
                            <?= $this->Form->control('last_name', [
                                'label' => false,
                                'class' => 'form-control' . ($user->hasErrors() && $user->getError('last_name') ? ' is-invalid' : ''),
                                'required' => true,
                            ]) ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <?= $this->Form->label('username', 'Username', ['class' => 'form-label']) ?>
                        <?= $this->Form->control('username', [
                            'label' => false,
                            'class' => 'form-control' . ($user->hasErrors() && $user->getError('username') ? ' is-invalid' : ''),
                            'required' => true,
                        ]) ?>
                    </div>

                    <div class="mb-3">
                        <?= $this->Form->label('email', 'Email', ['class' => 'form-label']) ?>
                        <?= $this->Form->control('email', [
                            'label' => false,
                            'type' => 'email',
                            'class' => 'form-control' . ($user->hasErrors() && $user->getError('email') ? ' is-invalid' : ''),
                            'required' => true,
                        ]) ?>
                    </div>

                    <div class="mb-3">
                        <?= $this->Form->label('password', 'Password', ['class' => 'form-label']) ?>
                        <?= $this->Form->control('password', [
                            'label' => false,
                            'class' => 'form-control' . ($user->hasErrors() && $user->getError('password') ? ' is-invalid' : ''),
                            'required' => true,
                        ]) ?>
                    </div>

                    <div class="mb-3">
                        <?= $this->Form->label('phone', 'Phone Number', ['class' => 'form-label']) ?>
                        <?= $this->Form->control('phone', [
                            'label' => false,
                            'type' => 'tel',
                            'class' => 'form-control' . ($user->hasErrors() && $user->getError('phone') ? ' is-invalid' : ''),
                            'required' => true,
                        ]) ?>
                    </div>

                    <div class="mb-3">
                        <?= $this->Form->label('date_of_birth', 'Date of Birth', ['class' => 'form-label']) ?>
                        <?= $this->Form->control('date_of_birth', [
                            'label' => false,
                            'type' => 'date',
                            'class' => 'form-control' . ($user->hasErrors() && $user->getError('date_of_birth') ? ' is-invalid' : ''),
                        ]) ?>
                    </div>

                    <div class="mb-3">
                        <?= $this->Form->label('address', 'Address', ['class' => 'form-label']) ?>
                        <?= $this->Form->control('address', [
                            'label' => false,
                            'type' => 'textarea',
                            'rows' => 2,
                            'class' => 'form-control',
                        ]) ?>
                    </div>

                    <div class="d-grid mt-4">
                        <?= $this->Form->button('Create Account', ['class' => 'btn btn-dark']) ?>
                    </div>

                    <?= $this->Form->end() ?>

                    <div class="text-center mt-3">
                        Already have an account?
                        <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'login']) ?>">Log in</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
