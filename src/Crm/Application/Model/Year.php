<?php

declare(strict_types=1);

namespace App\Crm\Application\Model;

use DateTimeInterface;

/**
 * @package App\Crm\Application\Model
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
class Year
{
    /**
     * @var Month[]
     */
    private array $months = [];

    public function __construct(
        private DateTimeInterface $month
    ) {
        $monthDate = new \DateTimeImmutable($this->month->format('Y-01-01 01:00:00'), $this->month->getTimezone());
        for ($i = 1; $i < 13; $i++) {
            $tmp = $this->createMonth($monthDate);
            $this->setMonth($tmp);
            $monthDate = $monthDate->add(new \DateInterval('P1M'));
        }
    }

    public function getYear(): DateTimeInterface
    {
        return $this->month;
    }

    public function getMonth(\DateTimeInterface $month): Month
    {
        return $this->months['_' . $month->format('m')];
    }

    public function getDay(\DateTimeInterface $date): Day
    {
        return $this->getMonth($date)->getDay($date);
    }

    /**
     * @return Month[]
     */
    public function getMonths(): array
    {
        return array_values($this->months);
    }

    protected function createMonth(\DateTimeImmutable $month): Month
    {
        return new Month($month);
    }

    protected function setMonth(Month $month): void
    {
        $this->months['_' . $month->getMonth()->format('m')] = $month;
    }
}
