<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Calendar;

enum CalendarSourceType: string
{
    case GOOGLE = 'google';
    case ICAL = 'ical';
    case JSON = 'json';
    case TIMESHEET = 'timesheet';
}
