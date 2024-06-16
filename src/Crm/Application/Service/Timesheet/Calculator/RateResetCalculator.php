<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Timesheet\Calculator;

use App\Crm\Application\Service\Timesheet\CalculatorInterface;
use App\Crm\Domain\Entity\Timesheet;

final class RateResetCalculator implements CalculatorInterface
{
    public function calculate(Timesheet $record, array $changeset): void
    {
        // check if the rate was changed manually
        foreach (['hourlyRate', 'fixedRate', 'internalRate', 'rate'] as $field) {
            if (\array_key_exists($field, $changeset)) {
                return;
            }
        }

        // if no manual rate changed was applied:
        // check if a field changed, that is relevant for the rate calculation
        // reset all rates, because most users do not even see their rates and would not be able
        // to change the rate, even if they knew that the changed project has another base rate
        foreach (['project', 'activity', 'user'] as $field) {
            if (\array_key_exists($field, $changeset)) {
                $record->resetRates();
                break;
            }
        }
    }

    public function getPriority(): int
    {
        // needs to run before all other
        return 50;
    }
}
