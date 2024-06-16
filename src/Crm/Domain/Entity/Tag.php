<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'platform_crm_tags')]
#[ORM\UniqueConstraint(columns: ['name'])]
#[ORM\Entity(repositoryClass: 'App\Crm\Domain\Repository\TagRepository')]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
#[UniqueEntity('name')]
#[Serializer\ExclusionPolicy('all')]
class Tag
{
    use ColorTrait;

    /**
     * Internal Tag ID
     */
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    private ?int $id = null;
    /**
     * The tag name
     */
    #[ORM\Column(name: 'name', type: 'string', length: 100, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100, normalizer: 'trim')]
    #[Assert\Regex(pattern: '/,/', message: 'Tag name cannot contain comma', match: false)]
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    private ?string $name = null;
    #[ORM\Column(name: 'visible', type: 'boolean', nullable: false, options: [
        'default' => true,
    ])]
    #[Assert\NotNull]
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    private bool $visible = true;

    /**
     * This is ONLY here, so we can count the amount of timesheets.
     *
     * @var Collection<Timesheet>
     */
    #[ORM\ManyToMany(targetEntity: Timesheet::class, mappedBy: 'tags', fetch: 'EXTRA_LAZY')]
    private Collection $timesheets;

    public function __construct()
    {
        $this->timesheets = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(?string $tagName): self
    {
        $this->name = $tagName;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): void
    {
        $this->visible = $visible;
    }
}
