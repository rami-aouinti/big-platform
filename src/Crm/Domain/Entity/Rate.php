<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use App\User\Domain\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

trait Rate
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    #[OA\Property(ref: '#/components/schemas/User')]
    private ?User $user = null;
    #[ORM\Column(name: 'rate', type: 'float', nullable: false)]
    #[Assert\GreaterThanOrEqual(0)]
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    private float $rate = 0.00;
    #[ORM\Column(name: 'internal_rate', type: 'float', nullable: true)]
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    private ?float $internalRate = null;
    #[ORM\Column(name: 'fixed', type: 'boolean', nullable: false)]
    #[Assert\NotNull]
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    private bool $isFixed = false;

    public function __clone()
    {
        if ($this->id) {
            $this->id = null;
        }
    }

    /**
     * Get entry id, returns null for new entities which were not persisted.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setInternalRate(?float $rate): self
    {
        $this->internalRate = $rate;

        return $this;
    }

    public function getInternalRate(): ?float
    {
        return $this->internalRate;
    }

    public function isFixed(): bool
    {
        return $this->isFixed;
    }

    public function setIsFixed(bool $isFixed): self
    {
        $this->isFixed = $isFixed;

        return $this;
    }
}
