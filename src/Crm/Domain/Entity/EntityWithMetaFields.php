<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use Doctrine\Common\Collections\Collection;

interface EntityWithMetaFields
{
    /**
     * @return Collection|MetaTableTypeInterface[]
     */
    public function getMetaFields(): Collection;

    public function getMetaField(string $name): ?MetaTableTypeInterface;

    public function setMetaField(MetaTableTypeInterface $meta): self;
}
