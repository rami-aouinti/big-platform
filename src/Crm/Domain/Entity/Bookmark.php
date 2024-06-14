<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use App\User\Domain\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package App\Crm\Domain\Entity
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[ORM\Table(name: 'kimai2_bookmarks')]
#[ORM\UniqueConstraint(columns: ['user_id', 'name'])]
#[ORM\Entity(repositoryClass: 'App\Crm\Domain\Repository\BookmarkRepository')]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
#[UniqueEntity(fields: ['user', 'name'])]
class Bookmark
{
    public const string SEARCH_DEFAULT = 'search-default';
    public const string COLUMN_VISIBILITY = 'columns';
    public const string TIMESHEET = 'timesheet';

    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?User $user = null;
    #[ORM\Column(name: 'type', type: 'string', length: 20, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 20)]
    private ?string $type = null;
    #[ORM\Column(name: 'name', type: 'string', length: 50, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $name = null;
    #[ORM\Column(name: 'content', type: 'text', nullable: false)]
    private ?string $content = null;

    public function __clone()
    {
        if ($this->id) {
            $this->id = null;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setContent(array $content): void
    {
        $this->content = json_encode($content);
    }

    public function getContent(): array
    {
        if ($this->content === null) {
            return [];
        }

        return json_decode($this->content, true);
    }
}
