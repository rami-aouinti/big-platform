<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Entity\Timesheet;

final class TimesheetRestartPostEvent extends AbstractTimesheetEvent
{
    public function __construct(
        Timesheet $new,
        private Timesheet $original
    ) {
        parent::__construct($new);
    }

    public function getOriginalTimesheet(): Timesheet
    {
        return $this->original;
    }
}
