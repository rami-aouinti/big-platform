<?php

declare(strict_types=1);

namespace App\Crm\Application\Model;

use DateTimeInterface;

class Month
{
    /**
     * @var Day[]
     */
    private array $days = [];

    public function __construct(
        private \DateTimeInterface $month
    ) {
        $date = new \DateTimeImmutable($this->month->format('Y-m-01 00:00:00'), $month->getTimezone());
        $start = $date->format('m');
        while ($start === $date->format('m')) {
            $day = $this->createDay($date);
            $this->setDay($day);
            $date = $date->add(new \DateInterval('P1D'));
        }
    }

    public function getMonth(): DateTimeInterface
    {
        return $this->month;
    }

    public function getDay(DateTimeInterface $date): Day
    {
        return $this->days['_' . $date->format('d')];
    }

    /**
     * @return Day[]
     */
    public function getDays(): array
    {
        return array_values($this->days);
    }

    protected function createDay(\DateTimeImmutable $day): Day
    {
        return new Day($day);
    }

    protected function setDay(Day $day): void
    {
        $this->days['_' . $day->getDay()->format('d')] = $day;
    }
}
