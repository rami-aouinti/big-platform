<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Entity\Timesheet;

/**
 * @package App\Crm\Transport\Event
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class TimesheetDuplicatePreEvent extends AbstractTimesheetEvent
{
    public function __construct(
        Timesheet $new,
        private readonly Timesheet $original
    ) {
        parent::__construct($new);
    }

    public function getOriginalTimesheet(): Timesheet
    {
        return $this->original;
    }
}
