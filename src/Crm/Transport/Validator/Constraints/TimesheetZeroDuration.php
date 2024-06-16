<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

final class TimesheetZeroDuration extends TimesheetConstraint
{
    public const ZERO_DURATION_ERROR = 'kimai-timesheet-zero-duration-01';

    protected const ERROR_NAMES = [
        self::ZERO_DURATION_ERROR => 'Duration cannot be zero.',
    ];

    public string $message = 'Duration cannot be zero.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
