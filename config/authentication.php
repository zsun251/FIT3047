<?php
declare(strict_types=1);

use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Psr\Http\Message\ServerRequestInterface;

return function (ServerRequestInterface $request): AuthenticationServiceInterface {
    $service = new AuthenticationService();

    // Determine if login is by email or username
    $loginField = 'username';
    if ($request->is('post')) {
        $data = $request->getParsedBody();
        if (isset($data['username']) && str_contains($data['username'], '@')) {
            $loginField = 'email';
        }
    }

    // Load Session Authenticator (checks session first)
    $service->loadAuthenticator('Authentication.Session');

    // Load Cookie Authenticator (checks for remember me cookie)
    $service->loadAuthenticator('Authentication.Cookie', [
        'fields' => [
            'username' => 'username',
            'password' => 'password',
        ],
        'loginUrl' => '/users/login',
    ]);

    // Load Form Authenticator
    $service->loadAuthenticator('Authentication.Form', [
        'loginUrl' => '/users/login',
        'fields' => [
            'username' => 'username',
            'password' => 'password',
        ],
        'identifier' => [
            'className' => 'Authentication.Password',
            'fields' => [
                'username' => $loginField,
                'password' => 'password',
            ],
        ],
    ]);

    return $service;
};
