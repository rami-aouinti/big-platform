<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber\Actions;

use App\Crm\Transport\Event\PageActionsEvent;

final class TimesheetTeamSubscriber extends AbstractTimesheetSubscriber
{
    public static function getActionName(): string
    {
        return 'timesheet_team';
    }

    public function onActions(PageActionsEvent $event): void
    {
        $this->timesheetActions($event, 'admin_timesheet_edit', 'admin_timesheet_duplicate');
    }
}
