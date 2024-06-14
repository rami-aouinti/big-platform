<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

final class TimesheetLockdown extends TimesheetConstraint
{
    public const PERIOD_LOCKED = 'kimai-timesheet-lockdown-01';

    protected const ERROR_NAMES = [
        self::PERIOD_LOCKED => 'This period is locked, please choose a later date.',
    ];

    public string $message = 'This period is locked, please choose a later date.';
    public \DateTime|string|null $now;

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
