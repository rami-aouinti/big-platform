<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Application\Service\WorkingTime\Model\Year;
use App\Crm\Application\Service\WorkingTime\Model\YearPerUserSummary;
use App\Crm\Application\Service\WorkingTime\Model\YearSummary;
use Symfony\Contracts\EventDispatcher\Event;

final class WorkingTimeYearSummaryEvent extends Event
{
    public function __construct(
        private YearPerUserSummary $yearPerUserSummary,
        private \DateTimeInterface $until
    ) {
    }

    public function getYear(): Year
    {
        return $this->yearPerUserSummary->getYear();
    }

    public function getUntil(): \DateTimeInterface
    {
        return $this->until;
    }

    public function addSummary(YearSummary $yearSummary): void
    {
        $this->yearPerUserSummary->addSummary($yearSummary);
    }
}
