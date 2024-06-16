<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Authentication;

use App\User\Domain\Entity\User;
use Symfony\Component\Security\Core\Exception\LogicException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;

final class ApiTokenUpgradeBadge implements BadgeInterface
{
    /**
     * @param PasswordUpgraderInterface<User> $passwordUpgrader
     */
    public function __construct(
        private ?string $plaintextApiToken,
        private PasswordUpgraderInterface $passwordUpgrader
    ) {
    }

    public function getAndErasePlaintextApiToken(): string
    {
        $password = $this->plaintextApiToken;
        if ($password === null) {
            throw new LogicException('The api token is erased as another listener already used this badge.');
        }

        $this->plaintextApiToken = null;

        return $password;
    }

    /**
     * @return PasswordUpgraderInterface<User>
     */
    public function getPasswordUpgrader(): PasswordUpgraderInterface
    {
        return $this->passwordUpgrader;
    }

    public function isResolved(): bool
    {
        return true;
    }
}
