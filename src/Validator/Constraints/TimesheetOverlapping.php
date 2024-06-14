<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

final class TimesheetOverlapping extends TimesheetConstraint
{
    public const RECORD_OVERLAPPING = 'kimai-timesheet-overlapping-01';

    protected const ERROR_NAMES = [
        self::RECORD_OVERLAPPING => 'You already have an entry for this time.',
    ];

    public string $message = 'You already have an entry for this time.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
