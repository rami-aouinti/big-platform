<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Entity\Timesheet;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Base event class to used with timesheet manipulations.
 */
abstract class AbstractTimesheetEvent extends Event
{
    public function __construct(
        private Timesheet $timesheet
    ) {
    }

    public function getTimesheet(): Timesheet
    {
        return $this->timesheet;
    }
}
