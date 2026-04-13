<?php
declare(strict_types=1);

use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\Authenticator\FormAuthenticator;
use Authentication\Authenticator\SessionAuthenticator;
use Authentication\Identifier\PasswordIdentifier;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Routing\Router;


return function (ServerRequestInterface $request): AuthenticationServiceInterface {
    $fields = [
        'username' => 'username',
        'password' => 'password',
    ];

    $service = new AuthenticationService([
        'unauthenticatedRedirect' => '/users/login',
        'queryParam' => 'redirect',
        'identifiers' => [
            'password' => [
                'className' => PasswordIdentifier::class,
                'fields' => $fields,
            ],
        ],
        'authenticators' => [
            'session' => [
                'className' => SessionAuthenticator::class,
            ],
            'form' => [
                'className' => FormAuthenticator::class,
                'fields' => $fields,
                'loginUrl' => '/users/login',
            ],
        ],
    ]);

    return $service;
};
