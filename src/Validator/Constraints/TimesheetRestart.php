<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

final class TimesheetRestart extends TimesheetConstraint
{
    public const START_DISALLOWED = 'kimai-timesheet-restart-01';

    protected const ERROR_NAMES = [
        self::START_DISALLOWED => 'You are not allowed to start this timesheet record.',
    ];

    public string $message = 'You are not allowed to start this timesheet record.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
