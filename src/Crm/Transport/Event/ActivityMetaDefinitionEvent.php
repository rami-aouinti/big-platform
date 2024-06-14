<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Entity\Activity;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event can be used, to dynamically add meta fields to activities
 */
final class ActivityMetaDefinitionEvent extends Event
{
    public function __construct(
        private Activity $entity
    ) {
    }

    public function getEntity(): Activity
    {
        return $this->entity;
    }
}
