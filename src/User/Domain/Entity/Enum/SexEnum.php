<?php

declare(strict_types=1);

namespace App\User\Domain\Entity\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

/**
 * @package App\User\Domain\Entity\Enum
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
enum SexEnum: string implements DatabaseEnumInterface
{
    use GetValues;

    case Male = 'male';
    case Female = 'female';
    case Other = 'other';

    public static function getDefault(): self
    {
        return self::Male;
    }
}
