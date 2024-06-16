<?php

declare(strict_types=1);

namespace App\Plugin;

final class Plugin
{
    private ?PluginMetadata $metadata = null;

    public function __construct(
        private readonly PluginInterface $bundle
    ) {
    }

    public function getMetadata(): PluginMetadata
    {
        if ($this->metadata === null) {
            $this->metadata = new PluginMetadata($this->getPath());
        }

        return $this->metadata;
    }

    public function getPath(): string
    {
        return $this->bundle->getPath();
    }

    public function getName(): string
    {
        $meta = $this->getMetadata();
        if ($meta->getName() !== null) {
            return $meta->getName();
        }

        return $this->getId();
    }

    public function getId(): string
    {
        return $this->bundle->getName();
    }
}
