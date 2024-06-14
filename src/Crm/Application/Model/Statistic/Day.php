<?php

declare(strict_types=1);

namespace App\Crm\Application\Model\Statistic;

use DateTime;

final class Day extends Timesheet
{
    private int $totalDurationBillable = 0;
    private array $details = [];

    public function __construct(
        private DateTime $day,
        int $duration,
        float $rate
    ) {
        $this->setTotalDuration($duration);
        $this->setTotalRate($rate);
    }

    public function getDay(): DateTime
    {
        return $this->day;
    }

    public function getTotalDurationBillable(): int
    {
        return $this->totalDurationBillable;
    }

    public function setTotalDurationBillable(int $seconds): void
    {
        $this->totalDurationBillable = $seconds;
    }

    public function setDetails(array $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}
