<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Timesheet\Calculator;

use App\Crm\Application\Service\Timesheet\CalculatorInterface;
use App\Crm\Application\Service\Timesheet\RoundingService;
use App\Crm\Domain\Entity\Timesheet;

/**
 * Implementation to calculate the durations for a timesheet record.
 */
final class DurationCalculator implements CalculatorInterface
{
    public function __construct(
        private RoundingService $roundings
    ) {
    }

    public function calculate(Timesheet $record, array $changeset): void
    {
        if ($record->getEnd() === null) {
            return;
        }

        $duration = $record->getCalculatedDuration();
        $record->setDuration($duration);

        $this->roundings->applyRoundings($record);
    }

    public function getPriority(): int
    {
        return 200;
    }
}
