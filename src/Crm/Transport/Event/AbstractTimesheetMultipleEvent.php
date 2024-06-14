<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Entity\Timesheet;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Base event class to used with timesheet manipulations.
 */
abstract class AbstractTimesheetMultipleEvent extends Event
{
    /**
     * @param array<Timesheet> $timesheets
     */
    public function __construct(
        private array $timesheets
    ) {
    }

    public function getTimesheets(): array
    {
        return $this->timesheets;
    }
}
