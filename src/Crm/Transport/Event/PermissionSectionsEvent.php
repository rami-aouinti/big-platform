<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Application\Model\PermissionSectionInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event can be used, to dynamically add sections to the permission screen.
 */
final class PermissionSectionsEvent extends Event
{
    /**
     * @var array<PermissionSectionInterface>
     */
    private array $sections = [];

    public function addSection(PermissionSectionInterface $section): self
    {
        $this->sections[] = $section;

        return $this;
    }

    /**
     * @return PermissionSectionInterface[]
     */
    public function getSections(): array
    {
        return $this->sections;
    }
}
