<?php

declare(strict_types=1);

namespace App\Crm\Application\Voter;

use App\Admin\Auth\Security\RolePermissionManager;
use App\User\Domain\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, null>
 */
final class ReportingVoter extends Voter
{
    private const ALLOWED_ATTRIBUTES = [
        'report:customer',
        'report:other',
        'report:project',
        'report:user',
    ];

    public function __construct(
        private readonly RolePermissionManager $permissionManager
    ) {
    }

    public function supportsAttribute(string $attribute): bool
    {
        return \in_array($attribute, self::ALLOWED_ATTRIBUTES, true);
    }

    public function supportsType(string $subjectType): bool
    {
        return $subjectType === 'null';
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject === null && $this->supportsAttribute($attribute);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $permissions = ['view_reporting'];

        switch ($attribute) {
            case 'report:customer':
                $permissions[] = 'customer_reporting';
                break;
            case 'report:other':
                $permissions[] = 'view_other_reporting';
                $permissions[] = 'view_other_timesheet';
                break;
            case 'report:project':
                $permissions[] = 'project_reporting';
                break;
            case 'report:user':
                // own reports are always allowed if reporting can be accessed
                break;
        }

        foreach ($permissions as $permission) {
            if (!$this->permissionManager->hasRolePermission($user, $permission)) {
                return false;
            }
        }

        return true;
    }
}
