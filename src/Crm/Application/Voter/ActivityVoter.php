<?php

declare(strict_types=1);

namespace App\Crm\Application\Voter;

use App\Admin\Auth\Security\RolePermissionManager;
use App\Crm\Domain\Entity\Activity;
use App\Crm\Domain\Entity\Team;
use App\User\Domain\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * A voter to check permissions on Activities.
 *
 * @extends Voter<string, Activity>
 */
final class ActivityVoter extends Voter
{
    /**
     * support rules based on the given activity
     */
    private const ALLOWED_ATTRIBUTES = [
        'view',
        'edit',
        'budget',
        'time',
        'delete',
        'permissions',
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
        return str_contains($subjectType, Activity::class);
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Activity && $this->supportsAttribute($attribute);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->permissionManager->hasRolePermission($user, $attribute . '_activity')) {
            return true;
        }

        // those cannot be assigned to teams
        if (\in_array($attribute, ['create', 'delete'], true)) {
            return false;
        }

        $hasTeamleadPermission = $this->permissionManager->hasRolePermission($user, $attribute . '_teamlead_activity');
        $hasTeamPermission = $this->permissionManager->hasRolePermission($user, $attribute . '_team_activity');

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

        // new and global activities have no project
        if (null === ($project = $subject->getProject())) {
            return false;
        }

        /** @var Team $team */
        foreach ($project->getTeams() as $team) {
            if ($hasTeamleadPermission && $user->isTeamleadOf($team)) {
                return true;
            }

            if ($hasTeamPermission && $user->isInTeam($team)) {
                return true;
            }
        }

        if (null === ($customer = $project->getCustomer())) {
            return false;
        }

        /** @var Team $team */
        foreach ($customer->getTeams() as $team) {
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
