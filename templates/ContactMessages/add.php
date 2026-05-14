<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ContactMessage $contactMessage
 */
?>
<?php $this->assign('title', $this->ContentBlock->text('contact-page-title')) ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <div class="contact-card">
                    <h2 class="fw-bold mb-2"><?= $this->ContentBlock->text('contact-page-title') ?></h2>
                    <p class="text-muted mb-4"><?= $this->ContentBlock->text('contact-intro-text') ?></p>
                    <p class="text-muted small mb-4">
                        <span class="text-danger">*</span> <?= $this->ContentBlock->text('contact-required-fields-text') ?>
                    </p>

                    <?= $this->Form->create($contactMessage, [
                        'class' => 'needs-validation recaptcha-form',
                        'novalidate' => true
                    ]) ?>

                    <div class="row mb-3">
                        <!-- First Name -->
                        <div class="col">
                            <?= $this->Form->control('first_name', [
                                'label' => 'First Name <span class="text-danger">*</span>',
                                'escape' => false,
                                'class' => 'form-control',
                                'required' => true,
                                'placeholder' => 'Jane',
                                'pattern' => "[a-zA-Z' -]+",
                                'title' => 'Only letters, apostrophes, hyphens, and spaces are allowed. Numbers are not allowed.'
                            ]) ?>
                        </div>

                        <!-- Last Name -->
                        <div class="col">
                            <?= $this->Form->control('last_name', [
                                'label' => 'Last Name <span class="text-danger">*</span>',
                                'escape' => false,
                                'class' => 'form-control',
                                'required' => true,
                                'placeholder' => 'Doe',
                                'pattern' => "[a-zA-Z' -]+",
                                'title' => 'Only letters, apostrophes, hyphens, and spaces are allowed. Numbers are not allowed.'
                            ]) ?>
                        </div>
                    </div>

                    <!-- Contact number -->
                    <div class="mb-3">
                        <?= $this->Form->control('contact_number', [
                            'label' => 'Contact Number <span class="text-danger">*</span>',
                            'escape' => false,
                            'class' => 'form-control',
                            'placeholder' => '04xx xxx xxx',
                            'type' => 'tel',
                            'pattern' => '[0-9]*',
                            'inputmode' => 'numeric',
                            'maxlength' => 10,
                            'title' => 'Phone number must contain digits only (0–9)'
                        ]) ?>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <?= $this->Form->control('email', [
                            'label' => 'Email <span class="text-danger">*</span>',
                            'escape' => false,
                            'type'  => 'email',
                            'class' => 'form-control',
                            'required' => true,
                            'placeholder' => 'you@example.com'
                        ]) ?>
                    </div>

                    <!-- Message -->
                    <div class="mb-4">
                        <?= $this->Form->control('message', [
                            'label' => 'Message <span class="text-danger">*</span>',
                            'escape' => false,
                            'type'        => 'textarea',
                            'class'       => 'form-control' . ($contactMessage->hasErrors() && $contactMessage->getError('message') ? ' is-invalid' : ''),
                            'rows'        => 5,
                            'placeholder' => $this->ContentBlock->text('contact-message-placeholder'),
                            'required'    => true,
                        ]) ?>
                    </div>

                    <!-- Hidden default status -->
                    <?= $this->Form->hidden('status', ['value' => 'unread']) ?>

                    <!-- Cloudflare Turnstile widget -->
                    <?= $this->Turnstile->hiddenField() ?>
                    <?= $this->Turnstile->widget() ?>

                    <!-- Submit button -->
                    <?= $this->Form->button($this->ContentBlock->text('contact-submit-button-text'), [
                        'class' => 'btn btn-primary btn-lg w-100 fw-bold'
                    ]) ?>

                    <?= $this->Form->end() ?>
                </div>

            </div>
        </div>
    </div>

<?php
// Load Turnstile scripts
$this->Turnstile->script();
?>?>
