<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Timesheet\TrackingMode;

use App\Crm\Domain\Entity\Timesheet;
use DateTimeZone;

trait TrackingModeTrait
{
    protected function getTimezone(Timesheet $timesheet): DateTimeZone
    {
        if ($timesheet->getBegin() !== null) {
            return $timesheet->getBegin()->getTimezone();
        }

        $timezone = date_default_timezone_get();

        if ($timesheet->getUser() !== null) {
            $timezone = $timesheet->getUser()->getTimezone();
        }

        return new DateTimeZone($timezone);
    }
}
