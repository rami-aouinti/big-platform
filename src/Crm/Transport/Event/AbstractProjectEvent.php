<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Entity\Project;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Base event class to used with project manipulations.
 */
abstract class AbstractProjectEvent extends Event
{
    public function __construct(
        private Project $project
    ) {
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}
