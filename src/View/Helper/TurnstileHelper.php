<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;

class TurnstileHelper extends Helper
{
    public array $helpers = ['Html', 'Form'];

    /**
     * Renders the Cloudflare Turnstile widget container.
     */
    public function widget(): string
    {
        $siteKey = Configure::read('Turnstile.siteKey');

        return sprintf(
            '<div class="cf-turnstile mb-3" data-sitekey="%s" data-callback="onTurnstileSuccess"></div>',
            h($siteKey)
        );
    }

    /**
     * Renders the hidden token field for the form.
     */
    public function hiddenField(): string
    {
        return $this->Form->hidden('cf_turnstile_response', [
            'id' => 'cf-turnstile-response',
        ]);
    }

    /**
     * Loads the Turnstile script.
     */
    public function script(): void
    {
        // Cloudflare Turnstile API script
        $this->Html->script(
            'https://challenges.cloudflare.com/turnstile/v0/api.js',
            ['block' => 'script', 'async' => true, 'defer' => true]
        );

        // Callback to populate hidden field with token
        $this->Html->scriptBlock(
            "function onTurnstileSuccess(token) {
                document.getElementById('cf-turnstile-response').value = token;
            }",
            ['block' => 'script']
        );
    }
}
