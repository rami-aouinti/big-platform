<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

/**
 * @package App\Crm\Transport\Validator\Constraints
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class TimesheetOverlapping extends TimesheetConstraint
{
    public const string RECORD_OVERLAPPING = 'kimai-timesheet-overlapping-01';

    protected const array ERROR_NAMES = [
        self::RECORD_OVERLAPPING => 'You already have an entry for this time.',
    ];

    public string $message = 'You already have an entry for this time.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
