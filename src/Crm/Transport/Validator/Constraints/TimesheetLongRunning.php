<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

final class TimesheetLongRunning extends TimesheetConstraint
{
    public const LONG_RUNNING = 'kimai-timesheet-long-running-01';
    public const MAXIMUM = 'kimai-timesheet-long-running-02';

    protected const ERROR_NAMES = [
        self::LONG_RUNNING => 'TIMESHEET_LONG_RUNNING',
        self::MAXIMUM => 'MAXIMUM',
    ];

    public string $message = 'Maximum duration of {{ value }} hours exceeded.';
    public string $maximumMessage = 'Maximum duration exceeded.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
