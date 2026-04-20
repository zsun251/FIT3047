<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ContactMessage $contactMessage
 */
?>
<?php $this->assign('title', 'Contact Us') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <div class="contact-card">
                <h2 class="fw-bold mb-2">Contact Us</h2>
                <p class="text-muted mb-4">Have a question? Send us a message and we'll get back to you.</p>

                <?= $this->Form->create($contactMessage, [
                    'class' => 'needs-validation recaptcha-form',
                    'novalidate' => true
                ]) ?>

                <div class="row mb-3">
                    <!-- First Name -->
                    <div class="col">
                        <?= $this->Form->control('first_name', [
                            'label' => 'First Name',
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
                            'label' => 'Last Name',
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
                        'label' => 'Contact Number',
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
                        'label' => 'Email',
                        'type'  => 'email',
                        'class' => 'form-control',
                        'required' => true,
                        'placeholder' => 'you@example.com'
                    ]) ?>
                </div>

                <!-- Message -->
                <div class="mb-4">
                    <?= $this->Form->control('message', [
                        'label'       => 'Message',
                        'type'        => 'textarea',
                        'class'       => 'form-control' . ($contactMessage->hasErrors() && $contactMessage->getError('message') ? ' is-invalid' : ''),
                        'rows'        => 5,
                        'placeholder' => 'How can we help you?',
                        'required'    => true,
                    ]) ?>
                </div>

                <!-- Hidden default status -->
                <?= $this->Form->hidden('status', ['value' => 'unread']) ?>

                <!-- reCAPTCHA hidden field -->
                <?= $this->Recaptcha->hiddenField() ?>

                <!-- Submit button -->
                <?= $this->Form->button('Send Message', [
                    'class' => 'btn btn-primary btn-lg w-100 fw-bold'
                ]) ?>

                <?= $this->Form->end() ?>
            </div>

        </div>
    </div>
</div>

<?php
// Load reCAPTCHA scripts
$this->Recaptcha->script('.recaptcha-form', 'contact');
?>
