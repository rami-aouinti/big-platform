<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Result;

/**
 * @package App\Crm\Domain\Repository\Result
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final readonly class TimesheetResultStatistic
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
