<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Repository\Query\TimesheetQuery;

/**
 * Dynamically find possible meta fields for a timesheet query.
 *
 * @method TimesheetQuery getQuery()
 */
final class TimesheetMetaDisplayEvent extends AbstractMetaDisplayEvent
{
    public const EXPORT = 'export';
    public const TIMESHEET = 'timesheet';
    public const TEAM_TIMESHEET = 'team-timesheet';
    public const TIMESHEET_EXPORT = 'timesheet-export';
    public const TEAM_TIMESHEET_EXPORT = 'team-timesheet-export';

    public function __construct(TimesheetQuery $query, string $location)
    {
        parent::__construct($query, $location);
    }
}
