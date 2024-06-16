<?php

declare(strict_types=1);

namespace App\Admin\Auth\Security;

use App\Admin\Auth\Ldap\LdapUserProvider;
use App\Configuration\SystemConfiguration;
use App\User\Domain\Entity\User;
use Symfony\Component\Security\Core\User\ChainUserProvider;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @template-implements PasswordUpgraderInterface<User>
 * @template-implements UserProviderInterface<User>
 */
final class KimaiUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    private ?ChainUserProvider $provider = null;

    /**
     * @param iterable<UserProviderInterface<User>> $providers
     */
    public function __construct(
        private readonly iterable $providers,
        private readonly SystemConfiguration $configuration
    ) {
    }

    public function getProviders(): array
    {
        return $this->getInternalProvider()->getProviders();
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->getInternalProvider()->loadUserByIdentifier($identifier); // @phpstan-ignore-line
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->getInternalProvider()->refreshUser($user); // @phpstan-ignore-line
    }

    public function supportsClass(string $class): bool
    {
        return $this->getInternalProvider()->supportsClass($class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        $this->getInternalProvider()->upgradePassword($user, $newHashedPassword);
    }

    private function getInternalProvider(): ChainUserProvider
    {
        if ($this->provider === null) {
            $activated = [];
            foreach ($this->providers as $provider) {
                if ($provider instanceof LdapUserProvider) {
                    if (!class_exists('Laminas\Ldap\Ldap')) {
                        continue;
                    }
                    if (!$this->configuration->isLdapActive()) {
                        continue;
                    }
                }
                $activated[] = $provider;
            }
            $this->provider = new ChainUserProvider(new \ArrayIterator($activated));
        }

        return $this->provider;
    }
}
