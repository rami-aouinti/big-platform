<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Timesheet;

use App\Crm\Domain\Entity\Timesheet;

/**
 * Implementation to calculate the rate for a timesheet record.
 */
interface RateServiceInterface
{
    public function calculate(Timesheet $record): Rate;
}
