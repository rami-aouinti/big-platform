<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository;

use App\User\Domain\Entity\User;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @template-implements PasswordUpgraderInterface<User>
 */
class ApiUserRepository implements UserLoaderInterface, PasswordUpgraderInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        return $this->userRepository->loadUserByIdentifier($identifier);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface|UserInterface $user, string $newHashedPassword): void
    {
        if (!($user instanceof User)) {
            return;
        }

        try {
            $user->setApiToken($newHashedPassword);
            $this->userRepository->saveUser($user);
        } catch (\Exception $ex) {
            // happens during login: if it fails, ignore it!
        }
    }
}
