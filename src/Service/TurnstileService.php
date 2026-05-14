<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Log\Log;

class TurnstileService
{
    /**
     * Verifies a Cloudflare Turnstile token.
     *
     * @param string $token The token submitted from the form.
     * @param string|null $remoteIp Optional: user's IP address for additional verification.
     * @return bool
     */
    public function verify(string $token, ?string $remoteIp = null): bool
    {
        if (empty($token)) {
            return false;
        }

        $secretKey = Configure::read('Turnstile.secretKey');

        $http = new Client();

        $data = [
            'secret' => $secretKey,
            'response' => $token,
        ];

        if ($remoteIp) {
            $data['remoteip'] = $remoteIp;
        }

        $response = $http->post(
            'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            $data
        );

        $body = json_decode($response->getStringBody(), true);

        if (!empty($body['success'])) {
            return true;
        }

        // Log errors for debugging
        if (!empty($body['error-codes'])) {
            Log::warning('Turnstile verification failed: ' . implode(', ', $body['error-codes']));
        }

        return false;
    }
}
