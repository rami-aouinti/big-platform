<?php

declare(strict_types=1);

namespace App\Utils;

interface EquatableInterface
{
    public function isEqualTo(object $compare): bool;
}
