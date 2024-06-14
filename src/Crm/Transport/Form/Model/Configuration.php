<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Model;

use App\Crm\Transport\Form\Type\YesNoType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraint;

final class Configuration
{
    private ?string $label = null;
    private string $translationDomain = 'messages';
    private string|int|null|bool|float $value = null;
    private ?string $type = null;
    /**
     * @var array<string, mixed>
     */
    private array $options = [];
    private bool $enabled = true;
    private bool $required = true;
    /**
     * @var Constraint[]
     */
    private array $constraints = [];

    public function __construct(
        private string $name
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string|int|null|bool|float
    {
        return $this->value;
    }

    public function setValue(string|int|null|bool|float $value): self
    {
        if ($this->type === CheckboxType::class || $this->type === YesNoType::class) {
            $value = (bool)$value;
        }

        $this->value = $value;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Constraint[]
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * @param Constraint[] $constraints
     */
    public function setConstraints(array $constraints): self
    {
        $this->constraints = $constraints;

        return $this;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function setTranslationDomain(string $translationDomain): self
    {
        $this->translationDomain = $translationDomain;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }
}
