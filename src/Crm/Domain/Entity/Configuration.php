<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package App\Crm\Domain\Entity
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[ORM\Table(name: 'platform_crm_configuration')]
#[ORM\UniqueConstraint(columns: ['name'])]
#[ORM\Entity(repositoryClass: 'App\Crm\Domain\Repository\ConfigurationRepository')]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
#[UniqueEntity('name')]
class Configuration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;
    #[ORM\Column(name: 'name', type: 'string', length: 100, nullable: false)]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 100)]
    private ?string $name = null;
    #[ORM\Column(name: 'value', type: 'text', length: 65535, nullable: true)]
    #[Assert\Length(max: 65535)]
    private ?string $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Given $value will not be serialized before its stored,
     * so it should be a scalar type that can be casted to string.
     */
    public function setValue(string|int|bool|null $value): self
    {
        if ($value === null) {
            $this->value = null;
        } elseif ($value === false) {
            $this->value = '0';
        } else {
            $this->value = (string)$value;
        }

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
