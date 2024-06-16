<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Timesheet\Calculator;

use App\Crm\Application\Service\Timesheet\CalculatorInterface;
use App\Crm\Application\Service\Timesheet\RateServiceInterface;
use App\Crm\Domain\Entity\Timesheet;

/**
 * Implementation to calculate the rate for a timesheet record.
 */
final class RateCalculator implements CalculatorInterface
{
    public function __construct(
        private RateServiceInterface $service
    ) {
    }

    public function calculate(Timesheet $record, array $changeset): void
    {
        $rate = $this->service->calculate($record);

        $record->setRate($rate->getRate());
        $record->setInternalRate($rate->getInternalRate());

        if ($rate->getHourlyRate() !== null) {
            $record->setHourlyRate($rate->getHourlyRate());
        }

        if ($rate->getFixedRate() !== null) {
            $record->setFixedRate($rate->getFixedRate());
        }
    }

    public function getPriority(): int
    {
        return 300;
    }
}
