<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;

class RecaptchaHelper extends Helper
{
    public array $helpers = ['Html', 'Form'];

    /**
     * Renders the hidden token field inside a form.
     */
    public function hiddenField(): string
    {
        return $this->Form->hidden('g_recaptcha_response', [
            'id' => 'g-recaptcha-response-field',
        ]);
    }

    /**
     * Loads the reCAPTCHA script and attaches the submit listener.
     *
     * @param string $formSelector  CSS selector for the form to protect.
     * @param string $action        reCAPTCHA action name (used for analytics in Google console).
     */
    public function script(string $formSelector = '.recaptcha-form', string $action = 'submit'): void
    {
        $siteKey = Configure::read('Recaptcha.siteKey');

        // Inline JS — attaches listener and injects token on submit
        $this->Html->scriptBlock(
            "grecaptcha.ready(function() {
                document.querySelector('{$formSelector}').addEventListener('submit', function(e) {
                    e.preventDefault();
                    var form = this;
                    grecaptcha.execute('{$siteKey}', {action: '{$action}'})
                        .then(function(token) {
                            document.getElementById('g-recaptcha-response-field').value = token;
                            form.submit();
                        });
                });
            });",
            ['block' => 'script']
        );

        // Google reCAPTCHA API script
        $this->Html->script(
            'https://www.google.com/recaptcha/api.js?render=' . $siteKey,
            ['block' => 'script']
        );
    }
}
