<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class TimeFormat extends Constraint
{
    public const INVALID_FORMAT = 'kimai-time-00';

    protected const ERROR_NAMES = [
        self::INVALID_FORMAT => 'The given value is not a valid time.',
    ];

    public string $message = 'This time format is invalid.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
