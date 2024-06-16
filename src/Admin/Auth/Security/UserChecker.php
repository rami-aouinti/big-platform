<?php

declare(strict_types=1);

namespace App\Admin\Auth\Security;

use App\User\Domain\Entity\User;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Advanced checks during authentication to make sure the user is allowed to use Kimai.
 */
final class UserChecker implements UserCheckerInterface
{
    /**
     * @throws AccountStatusException
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (!($user instanceof User)) {
            return;
        }

        if (!$user->isEnabled()) {
            $ex = new DisabledException('User account is disabled.');
            $ex->setUser($user);

            throw $ex;
        }
    }

    /**
     * @throws AccountStatusException
     */
    public function checkPostAuth(UserInterface $user): void
    {
        if (!($user instanceof User)) {
            return;
        }

        if (!$user->isEnabled()) {
            $ex = new DisabledException('User account is disabled.');
            $ex->setUser($user);

            throw $ex;
        }
    }
}
