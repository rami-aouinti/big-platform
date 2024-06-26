<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class HexColor extends Constraint
{
    public const HEX_COLOR_ERROR = 'xd5hffg-dsfef3-426a-83d7-2g8jkfr56d84';

    protected const ERROR_NAMES = [
        self::HEX_COLOR_ERROR => 'HEX_COLOR_ERROR',
    ];

    public string $message = 'The given value is not a valid hexadecimal color.';
}
