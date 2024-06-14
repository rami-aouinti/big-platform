<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Application\Service\WorkingTime\Model\Year;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Working time for every day of the given year.
 * Will be reflected in the working-time summary row.
 */
final class WorkingTimeYearEvent extends Event
{
    public function __construct(
        private Year $year,
        private \DateTimeInterface $until
    ) {
    }

    public function getUntil(): \DateTimeInterface
    {
        return $this->until;
    }

    public function getYear(): Year
    {
        return $this->year;
    }
}
