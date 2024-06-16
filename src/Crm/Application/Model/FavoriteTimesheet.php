<?php

declare(strict_types=1);

namespace App\Crm\Application\Model;

use App\Crm\Domain\Entity\Timesheet;

final class FavoriteTimesheet
{
    public function __construct(
        private Timesheet $timesheet,
        private bool $isFavorite
    ) {
    }

    public function getTimesheet(): Timesheet
    {
        return $this->timesheet;
    }

    public function isFavorite(): bool
    {
        return $this->isFavorite;
    }
}
