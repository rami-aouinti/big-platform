<?php

declare(strict_types=1);

namespace App\User;

use App\Crm\Domain\Repository\RolePermissionRepository;
use App\Entity\RolePermission;
use App\Role\Domain\Entity\Role;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Caches permissions, which rarely change once Kimai is setup.
 *
 * @final
 */
class PermissionService
{
    /**
     * @var null|array<int, array<string, string|bool>>
     */
    private ?array $cacheAll = null;

    public function __construct(
        private RolePermissionRepository $repository,
        private CacheInterface $cache
    ) {
    }

    public function saveRolePermission(RolePermission $permission): void
    {
        $this->repository->saveRolePermission($permission);
        $this->cache->delete('permissions');
    }

    public function findRolePermission(Role $role, string $permission): ?RolePermission
    {
        return $this->repository->findRolePermission($role, $permission);
    }

    /**
     * @return array<int, array<string, string|bool>>
     */
    public function getPermissions(): array
    {
        if ($this->cacheAll === null) {
            $this->cacheAll = $this->cache->get('permissions', function (ItemInterface $item) {
                $item->expiresAfter(86400); // one day

                return $this->repository->getAllAsArray();
            });
        }

        return $this->cacheAll;
    }
}
