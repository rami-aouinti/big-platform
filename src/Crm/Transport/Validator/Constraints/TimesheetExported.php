<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

final class TimesheetExported extends TimesheetConstraint
{
    public const TIMESHEET_EXPORTED = 'kimai-timesheet-exported-01';

    protected const ERROR_NAMES = [
        self::TIMESHEET_EXPORTED => 'This timesheet is already exported.',
    ];

    public string $message = 'This timesheet is already exported.';

    public null|\DateTime|string $now;

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
