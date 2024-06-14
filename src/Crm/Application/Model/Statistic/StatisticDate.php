<?php

declare(strict_types=1);

namespace App\Crm\Application\Model\Statistic;

final class StatisticDate extends Timesheet
{
    private \DateTimeInterface $date;
    private int $billableDuration = 0;
    private float $billableRate = 0.00;

    public function __construct(\DateTimeInterface $date)
    {
        $this->date = clone $date;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getBillableDuration(): int
    {
        return $this->billableDuration;
    }

    public function setBillableDuration(int $billableDuration): void
    {
        $this->billableDuration = $billableDuration;
    }

    public function getBillableRate(): float
    {
        return $this->billableRate;
    }

    public function setBillableRate(float $billableRate): void
    {
        $this->billableRate = $billableRate;
    }
}
