<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class TimesheetFutureTimes extends TimesheetConstraint
{
    public const BEGIN_IN_FUTURE_ERROR = 'kimai-timesheet-future-times-01';
    public const END_IN_FUTURE_ERROR = 'kimai-timesheet-future-times-02';

    protected const ERROR_NAMES = [
        self::BEGIN_IN_FUTURE_ERROR => 'The begin date cannot be in the future.',
        self::END_IN_FUTURE_ERROR => 'The end date cannot be in the future.',
    ];

    public string $message = 'The date cannot be in the future.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
