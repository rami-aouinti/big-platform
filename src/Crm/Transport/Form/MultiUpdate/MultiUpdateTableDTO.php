<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\MultiUpdate;

use Doctrine\Common\Collections\Collection;

class MultiUpdateTableDTO
{
    /**
     * @var array<object>|Collection<object>
     */
    private array|Collection $entities = [];
    /**
     * @var string[]
     */
    private array $actions = [
        '' => '',
    ];
    private ?string $action = null;

    /**
     * @return object[]
     */
    public function getEntities(): array|Collection
    {
        return $this->entities;
    }

    /**
     * @param array<object>|Collection<object> $entities
     */
    public function setEntities(array|Collection $entities): self
    {
        $this->entities = $entities;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function hasAction(): bool
    {
        return \count($this->actions) > 1;
    }

    public function addAction(string $label, string $url): self
    {
        $this->actions[$label] = $url;

        return $this;
    }

    public function addDelete(string $url): self
    {
        $this->actions['delete'] = $url;

        return $this;
    }

    public function addUpdate(string $url): self
    {
        $this->actions['action.edit'] = $url;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }
}
