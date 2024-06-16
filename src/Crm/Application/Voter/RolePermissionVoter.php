<?php

declare(strict_types=1);

namespace App\Crm\Application\Voter;

use App\Admin\Auth\Security\RolePermissionManager;
use App\User\Domain\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * A voter to check the free-configurable permission from "kimai.permissions".
 *
 * @extends Voter<string, null>
 */
final class RolePermissionVoter extends Voter
{
    public function __construct(
        private readonly RolePermissionManager $permissionManager
    ) {
    }

    public function supportsAttribute(string $attribute): bool
    {
        return $this->permissionManager->isRegisteredPermission($attribute);
    }

    public function supportsType(string $subjectType): bool
    {
        // we only work on single strings that have no subject
        return $subjectType === 'null';
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject === null && $this->supportsAttribute($attribute);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!($user instanceof User)) {
            return false;
        }

        return $this->permissionManager->hasRolePermission($user, $attribute);
    }
}
