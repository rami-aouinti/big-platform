<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\WorkingTime\Model;

use App\Crm\Application\Model\Year as BaseYear;

/**
 * @method array<MonthSummary> getMonths()
 * @method MonthSummary getMonth(\DateTimeInterface $month)
 */
final class YearSummary extends BaseYear
{
    public function __construct(
        \DateTimeInterface $month,
        private string $title
    ) {
        parent::__construct($month);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getExpectedTime(): int
    {
        $all = 0;
        foreach ($this->getMonths() as $month) {
            $all += $month->getExpectedTime();
        }

        return $all;
    }

    public function getActualTime(): int
    {
        $all = 0;
        foreach ($this->getMonths() as $month) {
            $all += $month->getActualTime();
        }

        return $all;
    }

    protected function createMonth(\DateTimeInterface $month): MonthSummary
    {
        return new MonthSummary($month);
    }
}
