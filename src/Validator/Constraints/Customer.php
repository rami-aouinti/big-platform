<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Customer extends Constraint
{
    public const CUSTOMER_NUMBER_EXISTING = 'kimai-customer-00';

    protected const ERROR_NAMES = [
        self::CUSTOMER_NUMBER_EXISTING => 'The number %number% is already used.',
    ];

    public string $message = 'This customer has invalid settings.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
