<?php

declare(strict_types=1);

namespace App\Admin\Auth\Security;

use Scheb\TwoFactorBundle\Security\TwoFactor\AuthenticationContextInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Condition\TwoFactorConditionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class TwoFactorCondition implements TwoFactorConditionInterface
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

    public function shouldPerformTwoFactorAuthentication(AuthenticationContextInterface $context): bool
    {
        // never require 2FA on API calls
        if (str_starts_with($context->getRequest()->getRequestUri(), '/api/')) {
            return false;
        }

        // if a user is remembered, it means he already passed the TOTP code
        // do not bother again with the code
        return !$this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }
}
