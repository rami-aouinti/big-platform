<?php

declare(strict_types=1);

namespace App\Admin\Auth\Ldap;

use App\User\Domain\Entity\User;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use function get_class;

/**
 * @template-implements UserProviderInterface<User>
 */
final class LdapUserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly LdapManager $ldapManager,
        private readonly ?LoggerInterface $logger = null
    ) {
    }

    /**
     * @throws Exception
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->ldapManager->findUserByUsername($identifier);

        if (empty($user)) {
            $this->logDebug('User {username} {result} on LDAP', [
                'action' => 'loadUserByIdentifier',
                'username' => $identifier,
                'result' => 'not found',
            ]);
            $ex = new UserNotFoundException(sprintf('User "%s" not found', $identifier));
            $ex->setUserIdentifier($identifier);

            throw $ex;
        }

        $this->logDebug('User {username} {result} on LDAP', [
            'action' => 'loadUserByIdentifier',
            'username' => $identifier,
            'result' => 'found',
        ]);

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!($user instanceof User)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        if (!$user->isLdapUser()) {
            throw new UnsupportedUserException(sprintf('Account "%s" is not a registered LDAP user.', $user->getUserIdentifier()));
        }

        try {
            $this->ldapManager->updateUser($user);
        } catch (LdapDriverException $ex) {
            throw new UnsupportedUserException(sprintf('Failed to refresh user "%s", probably DN is expired.', $user->getUserIdentifier()));
        }

        return $user;
    }

    /**
     * @param $class
     *
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return $class === User::class;
    }

    private function logDebug(string $message, array $context = []): void
    {
        if ($this->logger === null) {
            return;
        }

        $this->logger->debug($message, $context);
    }
}
