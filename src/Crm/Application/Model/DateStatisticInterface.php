<?php

declare(strict_types=1);

namespace App\Crm\Application\Model;

use App\Crm\Application\Model\Statistic\StatisticDate;
use DateTimeInterface;

interface DateStatisticInterface
{
    /**
     * For unified frontend access
     *
     * @return StatisticDate[]
     */
    public function getData(): array;

    public function getByDateTime(DateTimeInterface $date): ?StatisticDate;

    /**
     * @return DateTimeInterface[]
     */
    public function getDateTimes(): array;
}
