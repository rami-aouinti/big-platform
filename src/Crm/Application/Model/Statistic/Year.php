<?php

declare(strict_types=1);

namespace App\Crm\Application\Model\Statistic;

/**
 * @package App\Crm\Application\Model\Statistic
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class Year
{
    /**
     * @var Month[]
     */
    private array $months = [];

    public function __construct(
        private string $year
    ) {
    }

    public function getYear(): string
    {
        return $this->year;
    }

    public function setMonth(Month $month): self
    {
        $this->months[$month->getMonthNumber()] = $month;

        return $this;
    }

    public function getMonth(int $month): ?Month
    {
        if (isset($this->months[$month])) {
            return $this->months[$month];
        }

        return null;
    }

    /**
     * @return Month[]
     */
    public function getMonths(): array
    {
        return array_values($this->months);
    }

    public function getDuration(): int
    {
        $duration = 0;

        foreach ($this->months as $month) {
            $duration += $month->getDuration();
        }

        return $duration;
    }

    public function getBillableDuration(): int
    {
        $duration = 0;

        foreach ($this->months as $month) {
            $duration += $month->getBillableDuration();
        }

        return $duration;
    }

    public function getRate(): float
    {
        $rate = 0.0;

        foreach ($this->months as $month) {
            $rate += $month->getRate();
        }

        return $rate;
    }

    public function getBillableRate(): float
    {
        $rate = 0.0;

        foreach ($this->months as $month) {
            $rate += $month->getBillableRate();
        }

        return $rate;
    }

    public function getInternalRate(): float
    {
        $rate = 0.0;

        foreach ($this->months as $month) {
            $rate += $month->getInternalRate();
        }

        return $rate;
    }
}
