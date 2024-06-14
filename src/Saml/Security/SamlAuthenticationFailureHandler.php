<?php

declare(strict_types=1);

namespace App\Saml\Security;

use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;

final class SamlAuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
    protected $defaultOptions = [
        'failure_path' => 'login',
        'failure_forward' => false,
        'login_path' => 'saml_login',
        'failure_path_parameter' => '_failure_path',
    ];
}
