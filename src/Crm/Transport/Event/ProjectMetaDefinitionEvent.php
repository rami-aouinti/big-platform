<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Entity\Project;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event can be used, to dynamically add meta fields to projects
 */
final class ProjectMetaDefinitionEvent extends Event
{
    public function __construct(
        private readonly Project $entity
    ) {
    }

    public function getEntity(): Project
    {
        return $this->entity;
    }
}
