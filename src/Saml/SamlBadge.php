<?php

declare(strict_types=1);

namespace App\Saml;

use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;

final class SamlBadge implements BadgeInterface
{
    public function __construct(
        private readonly SamlLoginAttributes $samlToken
    ) {
    }

    public function getSamlLoginAttributes(): SamlLoginAttributes
    {
        return $this->samlToken;
    }

    public function isResolved(): bool
    {
        return true;
    }
}
