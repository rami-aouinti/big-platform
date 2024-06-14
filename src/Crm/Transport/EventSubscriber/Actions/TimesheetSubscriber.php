<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber\Actions;

use App\Crm\Transport\Event\PageActionsEvent;

final class TimesheetSubscriber extends AbstractTimesheetSubscriber
{
    public static function getActionName(): string
    {
        return 'timesheet';
    }

    public function onActions(PageActionsEvent $event): void
    {
        $this->timesheetActions($event, 'timesheet_edit', 'timesheet_duplicate');
    }
}
