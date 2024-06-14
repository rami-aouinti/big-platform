<?php

declare(strict_types=1);

namespace App\Configuration;

final class LdapConfiguration
{
    public function __construct(
        private SystemConfiguration $configuration
    ) {
    }

    public function isActivated(): bool
    {
        return $this->configuration->isLdapActive();
    }

    public function getRoleParameters(): array
    {
        return $this->configuration->findArray('ldap.role');
    }

    public function getUserParameters(): array
    {
        return $this->configuration->findArray('ldap.user');
    }

    public function getConnectionParameters(): array
    {
        return $this->configuration->findArray('ldap.connection');
    }
}
