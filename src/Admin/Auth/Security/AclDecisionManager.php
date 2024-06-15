<?php

declare(strict_types=1);

namespace App\Admin\Auth\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * Class AclDecisionManager
 *
 * @package App\Admin\Auth\Security
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final readonly class AclDecisionManager
{
    public function __construct(
        private AccessDecisionManagerInterface $decisionManager
    ) {
    }

    public function isFullyAuthenticated(TokenInterface $token): bool
    {
        if ($this->decisionManager->decide($token, ['IS_AUTHENTICATED_FULLY'])) {
            return true;
        }

        return false;
    }
}
