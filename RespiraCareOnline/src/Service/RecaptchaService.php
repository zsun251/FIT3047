<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Core\Configure;
use Cake\Http\Client;

class RecaptchaService
{
    /**
     * Verifies a reCAPTCHA v3 token against Google's API.
     *
     * @param string $token    The token submitted from the form.
     * @param string $action   The action name to validate against (e.g. 'contact').
     * @return bool
     */
    public function verify(string $token, string $action = 'contact'): bool
    {
        if (empty($token)) {
            return false;
        }

        $secretKey = Configure::read('Recaptcha.secretKey');
        $threshold  = Configure::read('Recaptcha.threshold') ?? 0.5;

        $http = new Client();
        $response = $http->post(
            'https://www.google.com/recaptcha/api/siteverify',
            ['secret' => $secretKey, 'response' => $token]
        );

        $body = json_decode($response->getStringBody(), true);

        return !empty($body['success'])
            && isset($body['score'])
            && $body['score'] >= $threshold
            && ($body['action'] ?? '') === $action;
    }
}
