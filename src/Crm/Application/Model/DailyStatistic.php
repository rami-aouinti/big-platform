<?php

declare(strict_types=1);

namespace App\Crm\Application\Model;

use App\Crm\Application\Model\Statistic\StatisticDate;
use App\User\Domain\Entity\User;
use DateTimeInterface;

final class DailyStatistic implements DateStatisticInterface
{
    /**
     * @var array<string, StatisticDate>
     */
    private array $days = [];
    private DateTimeInterface $begin;
    private DateTimeInterface $end;

    public function __construct(
        DateTimeInterface $begin,
        DateTimeInterface $end,
        private User $user
    ) {
        $this->begin = clone $begin;
        $this->end = clone $end;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return StatisticDate[]
     */
    public function getDays(): array
    {
        $this->setupDays();

        return array_values($this->days);
    }

    /**
     * For unified frontend access
     *
     * @return StatisticDate[]
     */
    public function getData(): array
    {
        return $this->getDays();
    }

    public function getDayByDateTime(\DateTimeInterface $date): ?StatisticDate
    {
        return $this->getDay($date->format('Y'), $date->format('m'), $date->format('d'));
    }

    public function getByDateTime(\DateTimeInterface $date): ?StatisticDate
    {
        return $this->getDayByDateTime($date);
    }

    public function getDayByReportDate(string $date): ?StatisticDate
    {
        $this->setupDays();

        if (!isset($this->days[$date])) {
            return null;
        }

        return $this->days[$date];
    }

    public function getDay(string $year, string $month, string $day): ?StatisticDate
    {
        if ((int)$month < 10) {
            $month = '0' . (int)$month;
        }

        if ((int)$day < 10) {
            $day = '0' . (int)$day;
        }

        $date = $year . '-' . $month . '-' . $day;

        return $this->getDayByReportDate($date);
    }

    /**
     * @return DateTimeInterface[]
     */
    public function getDateTimes(): array
    {
        $this->setupDays();
        $all = [];

        foreach ($this->days as $id => $day) {
            $all[] = $day->getDate();
        }

        return $all;
    }

    private function setupDays(): void
    {
        if (!empty($this->days)) {
            return;
        }

        $tmp = \DateTime::createFromInterface($this->begin);
        $tmp->setTime(0, 0, 0);
        while ($tmp < $this->end) {
            $id = $tmp->format('Y-m-d');
            $this->days[$id] = new StatisticDate(clone $tmp);
            $tmp->modify('+1 day');
        }
    }
}
