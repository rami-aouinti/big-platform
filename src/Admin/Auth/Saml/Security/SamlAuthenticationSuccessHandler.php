<?php

declare(strict_types=1);

namespace App\Admin\Auth\Saml\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

final class SamlAuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    protected $defaultOptions = [
        'always_use_default_target_path' => false,
        'default_target_path' => '/',
        'login_path' => 'saml_login',
        'target_path_parameter' => '_target_path',
        'use_referer' => false,
    ];

    protected function determineTargetUrl(Request $request): string
    {
        if ($this->options['always_use_default_target_path']) {
            return $this->options['default_target_path'];
        }

        $relayState = $request->get('RelayState');
        $loginUrl = $this->httpUtils->generateUri($request, $this->options['login_path']);

        if ($relayState !== null && $relayState !== '' && $relayState !== $loginUrl) {
            return $relayState;
        }

        return parent::determineTargetUrl($request);
    }
}
