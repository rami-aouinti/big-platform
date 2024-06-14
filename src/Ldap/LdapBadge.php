<?php

declare(strict_types=1);

namespace App\Ldap;

use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;

final class LdapBadge implements BadgeInterface
{
    private bool $resolved = false;

    public function markResolved(): void
    {
        $this->resolved = true;
    }

    public function isResolved(): bool
    {
        return $this->resolved;
    }
}
