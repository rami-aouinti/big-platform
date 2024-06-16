<?php

declare(strict_types=1);

namespace App\Crm\Application\Model;

use DateTimeImmutable;

class Day
{
    public function __construct(
        private DateTimeImmutable $day
    ) {
    }

    public function getDay(): DateTimeImmutable
    {
        return $this->day;
    }
}
