<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ContactMessage $contactMessage
 */

$this->assign('title', $this->ContentBlock->text('contact-page-title'));
$this->Html->css('contact', ['block' => true]);

$requiredFieldsText = trim((string)$this->ContentBlock->text('contact-required-fields-text'));
$requiredFieldsText = trim(ltrim($requiredFieldsText, "* \t\n\r\0\x0B"));
?>

    <section class="contact-page-shell">
        <div class="contact-card">
            <div class="contact-brand-panel">
                <div class="contact-brand-top">
                    <?= $this->ContentBlock->image('global-logo', [
                        'alt' => $this->ContentBlock->text('global-logo-alt'),
                        'class' => 'contact-logo-img'
                    ]) ?>

                    <h1 class="contact-brand-name">RespiraCare</h1>
                </div>

                <div class="contact-side-copy">
                    <h2><?= h($this->ContentBlock->text('contact-page-title')) ?></h2>
                    <p><?= h($this->ContentBlock->text('contact-intro-text')) ?></p>

                    <div class="contact-required-note">
                        <span><?= h($requiredFieldsText) ?><span class="contact-required-mark">*</span></span>
                    </div>
                </div>

                <div class="contact-side-highlights">
                    <div class="contact-highlight">
                    <span class="contact-highlight-icon">
                        <i class="bi bi-clock-history"></i>
                    </span>
                        <div class="contact-highlight-copy">
                            <strong>Quick replies</strong>
                            <span>We usually respond within 1–2 business days.</span>
                        </div>
                    </div>

                    <div class="contact-highlight">
                    <span class="contact-highlight-icon">
                        <i class="bi bi-shield-check"></i>
                    </span>
                        <div class="contact-highlight-copy">
                            <strong>Secure contact</strong>
                            <span>Your message is sent directly to our team.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-form-panel">
                <?= $this->Flash->render() ?>

                <?= $this->Form->create($contactMessage, [
                    'class' => 'contact-form needs-validation recaptcha-form',
                    'novalidate' => true
                ]) ?>

                <div class="contact-name-grid">
                    <div class="contact-field">
                        <?= $this->Form->control('first_name', [
                            'label' => '<span class="contact-label-content"><i class="bi bi-person"></i><span>First Name<span class="contact-required-mark">*</span></span></span>',
                            'escape' => false,
                            'class' => 'form-control contact-input',
                            'required' => true,
                            'placeholder' => 'Jane',
                            'pattern' => "[a-zA-Z' -]+",
                            'title' => 'Only letters, apostrophes, hyphens, and spaces are allowed. Numbers are not allowed.'
                        ]) ?>
                    </div>

                    <div class="contact-field">
                        <?= $this->Form->control('last_name', [
                            'label' => '<span class="contact-label-content"><i class="bi bi-person"></i><span>Last Name<span class="contact-required-mark">*</span></span></span>',
                            'escape' => false,
                            'class' => 'form-control contact-input',
                            'required' => true,
                            'placeholder' => 'Doe',
                            'pattern' => "[a-zA-Z' -]+",
                            'title' => 'Only letters, apostrophes, hyphens, and spaces are allowed. Numbers are not allowed.'
                        ]) ?>
                    </div>
                </div>

                <div class="contact-details-grid">
                    <div class="contact-field">
                        <?= $this->Form->control('contact_number', [
                            'label' => '<span class="contact-label-content"><i class="bi bi-telephone"></i><span>Contact Number<span class="contact-required-mark">*</span></span></span>',
                            'escape' => false,
                            'class' => 'form-control contact-input',
                            'placeholder' => '04xx xxx xxx',
                            'type' => 'tel',
                            'pattern' => '[0-9]*',
                            'inputmode' => 'numeric',
                            'maxlength' => 10,
                            'title' => 'Phone number must contain digits only (0–9)'
                        ]) ?>
                    </div>

                    <div class="contact-field">
                        <?= $this->Form->control('email', [
                            'label' => '<span class="contact-label-content"><i class="bi bi-envelope"></i><span>Email<span class="contact-required-mark">*</span></span></span>',
                            'escape' => false,
                            'type' => 'email',
                            'class' => 'form-control contact-input',
                            'required' => true,
                            'placeholder' => 'janedoe@outlook.com'
                        ]) ?>
                    </div>
                </div>

                <div class="contact-field contact-message-field">
                    <?= $this->Form->control('message', [
                        'label' => '<span class="contact-label-content"><i class="bi bi-chat-left-text"></i><span>Message<span class="contact-required-mark">*</span></span></span>',
                        'escape' => false,
                        'type' => 'textarea',
                        'class' => 'form-control contact-input contact-textarea' . ($contactMessage->hasErrors() && $contactMessage->getError('message') ? ' is-invalid' : ''),
                        'rows' => 5,
                        'placeholder' => $this->ContentBlock->text('contact-message-placeholder'),
                        'required' => true,
                    ]) ?>
                </div>

                <?= $this->Form->hidden('status', ['value' => 'unread']) ?>

                <div class="contact-turnstile-wrap">
                    <?= $this->Turnstile->hiddenField() ?>
                    <?= $this->Turnstile->widget() ?>
                </div>

                <?= $this->Form->button(
                    '<i class="bi bi-send"></i><span>' . h($this->ContentBlock->text('contact-submit-button-text')) . '</span>',
                    [
                        'class' => 'btn contact-submit-button',
                        'escapeTitle' => false
                    ]
                ) ?>

                <?= $this->Form->end() ?>
            </div>
        </div>
    </section>

<?php
$this->Turnstile->script();
?>
