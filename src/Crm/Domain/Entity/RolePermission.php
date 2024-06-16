<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use App\Role\Domain\Entity\Role;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'platform_crm_roles_permissions')]
#[ORM\UniqueConstraint(name: 'role_permission', columns: ['role', 'permission'])]
#[ORM\Entity(repositoryClass: 'App\Crm\Domain\Repository\RolePermissionRepository')]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
#[UniqueEntity(['role', 'permission'])]
class RolePermission
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(
        name: 'role',
        referencedColumnName: 'role',
        onDelete: 'CASCADE',
    )]
    #[Assert\NotNull]
    private ?Role $role = null;
    #[ORM\Column(name: 'permission', type: 'string', length: 50, nullable: false)]
    #[Assert\Length(max: 50)]
    private ?string $permission = null;
    #[ORM\Column(name: 'allowed', type: 'boolean', nullable: false, options: [
        'default' => false,
    ])]
    #[Assert\NotNull]
    private bool $allowed = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getPermission(): ?string
    {
        return $this->permission;
    }

    public function setPermission(string $permission): self
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Alias for isValue()
     */
    public function isAllowed(): bool
    {
        return $this->allowed;
    }

    public function setAllowed(bool $allowed): self
    {
        $this->allowed = $allowed;

        return $this;
    }
}
