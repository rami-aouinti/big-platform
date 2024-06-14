<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Role extends Constraint
{
    public const ROLE_ERROR = 'xd5hffg-dsfef3-426a-83d7-1f2d33hs5d84';

    protected const ERROR_NAMES = [
        self::ROLE_ERROR => 'ROLE_ERROR',
    ];

    public string $message = 'This value is not a valid role.';
}
