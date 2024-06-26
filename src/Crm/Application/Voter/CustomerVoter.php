<?php

declare(strict_types=1);

namespace App\Crm\Application\Voter;

use App\Admin\Auth\Security\RolePermissionManager;
use App\Crm\Domain\Entity\Customer;
use App\Crm\Domain\Entity\Team;
use App\User\Domain\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * A voter to check authorization on Customers.
 *
 * @extends Voter<string, Customer>
 */
final class CustomerVoter extends Voter
{
    /**
     * supported attributes/rules based on the given customer
     */
    private const ALLOWED_ATTRIBUTES = [
        'view',
        'create',
        'edit',
        'budget',
        'time',
        'delete',
        'permissions',
        'comments',
        'details',
        'access',
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
        return str_contains($subjectType, Customer::class);
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Customer && $this->supportsAttribute($attribute);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // this is a virtual permission, only meant to be used by developer
        // it checks if access to the given customer is potentially possible
        if ($attribute === 'access') {
            if ($subject->getTeams()->count() === 0) {
                return true;
            }

            foreach ($subject->getTeams() as $team) {
                if ($user->isInTeam($team)) {
                    return true;
                }
            }

            if ($user->canSeeAllData()) {
                return true;
            }

            return false;
        }

        if ($this->permissionManager->hasRolePermission($user, $attribute . '_customer')) {
            return true;
        }

        // those cannot be assigned to teams
        if (\in_array($attribute, ['create', 'delete'])) {
            return false;
        }

        $hasTeamleadPermission = $this->permissionManager->hasRolePermission($user, $attribute . '_teamlead_customer');
        $hasTeamPermission = $this->permissionManager->hasRolePermission($user, $attribute . '_team_customer');

        if (!$hasTeamleadPermission && !$hasTeamPermission) {
            return false;
        }

        /** @var Team $team */
        foreach ($subject->getTeams() as $team) {
            if ($hasTeamleadPermission && $user->isTeamleadOf($team)) {
                return true;
            }

            if ($hasTeamPermission && $user->isInTeam($team)) {
                return true;
            }
        }

        return false;
    }
}
