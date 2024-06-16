<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Model;

final class SystemConfiguration
{
    private ?string $translation = null;
    private string $translationDomain = 'system-configuration';
    /**
     * @var Configuration[]
     */
    private array $configuration = [];

    public function __construct(
        private ?string $section = null
    ) {
    }

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function setTranslation(string $translation): self
    {
        $this->translation = $translation;

        return $this;
    }

    public function getTranslation(): string
    {
        return $this->translation ?? $this->section;
    }

    public function setTranslationDomain(string $domain): self
    {
        $this->translationDomain = $domain;

        return $this;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    /**
     * @return Configuration[]
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function getConfigurationByName(string $name): ?Configuration
    {
        foreach ($this->configuration as $configuration) {
            if ($configuration->getName() === $name) {
                return $configuration;
            }
        }

        return null;
    }

    /**
     * @param Configuration[] $configuration
     */
    public function setConfiguration(array $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function addConfiguration(Configuration $configuration): self
    {
        $this->configuration[] = $configuration;

        return $this;
    }
}
