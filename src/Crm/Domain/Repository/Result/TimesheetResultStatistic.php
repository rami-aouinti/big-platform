<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Result;

final class TimesheetResultStatistic
{
    public function __construct(
        private int $count,
        private int $duration
    ) {
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }
}
