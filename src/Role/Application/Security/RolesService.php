<?php

declare(strict_types=1);

namespace App\Role\Application\Security;

use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\Role\Domain\Enum\Role;
use BackedEnum;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

use function array_map;
use function array_unique;
use function array_values;
use function mb_strtolower;

/**
 * Class RolesService
 *
 * @package App\Role
 */
class RolesService implements RolesServiceInterface
{
    public function __construct(
        private readonly RoleHierarchyInterface $roleHierarchy,
    ) {
    }

    public function getRoles(): array
    {
        return array_map(static fn (BackedEnum $enum): string => $enum->value, Role::cases());
    }

    public function getRoleLabel(string $role): string
    {
        $enum = Role::tryFrom($role);

        return $enum instanceof Role ? $enum->label() : 'Unknown - ' . $role;
    }

    public function getShort(string $role): string
    {
        $enum = Role::tryFrom($role);

        return $enum instanceof Role ? mb_strtolower($enum->name) : 'Unknown - ' . $role;
    }

    public function getInheritedRoles(array $roles): array
    {
        return array_values(array_unique($this->roleHierarchy->getReachableRoleNames($roles)));
    }
}
