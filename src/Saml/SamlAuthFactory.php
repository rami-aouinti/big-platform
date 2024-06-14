<?php

declare(strict_types=1);

namespace App\Saml;

use App\Configuration\SamlConfigurationInterface;
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Utils;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @final
 */
class SamlAuthFactory
{
    public function __construct(
        private readonly RequestStack $request,
        private readonly SamlConfigurationInterface $configuration
    ) {
    }

    public function create(): Auth
    {
        if ($this->request->getMainRequest() !== null && $this->request->getMainRequest()->isFromTrustedProxy()) {
            Utils::setProxyVars(true);
        }

        return new Auth($this->configuration->getConnection());
    }
}
