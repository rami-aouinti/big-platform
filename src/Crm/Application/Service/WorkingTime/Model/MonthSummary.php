<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\WorkingTime\Model;

use App\Crm\Application\Model\Month as BaseMonth;

final class MonthSummary extends BaseMonth
{
    private int $expectedTime = 0;
    private int $actualTime = 0;

    public function getExpectedTime(): int
    {
        return $this->expectedTime;
    }

    public function setExpectedTime(int $expectedTime): void
    {
        $this->expectedTime = $expectedTime;
    }

    public function getActualTime(): int
    {
        return $this->actualTime;
    }

    public function setActualTime(int $actualTime): void
    {
        $this->actualTime = $actualTime;
    }
}
