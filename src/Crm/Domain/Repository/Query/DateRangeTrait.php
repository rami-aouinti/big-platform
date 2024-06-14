<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Query;

use App\Crm\Transport\Form\Model\DateRange;

trait DateRangeTrait
{
    protected ?DateRange $dateRange = null;

    public function getBegin(): ?\DateTime
    {
        return $this->dateRange?->getBegin();
    }

    public function setBegin(\DateTimeInterface $begin): void
    {
        $this->dateRange->setBegin(\DateTime::createFromInterface($begin));
    }

    public function getEnd(): ?\DateTime
    {
        return $this->dateRange?->getEnd();
    }

    public function setEnd(\DateTimeInterface $end): void
    {
        $this->dateRange->setEnd(\DateTime::createFromInterface($end));
    }

    public function getDateRange(): ?DateRange
    {
        return $this->dateRange;
    }

    public function setDateRange(DateRange $dateRange): void
    {
        $this->dateRange = $dateRange;
    }
}
