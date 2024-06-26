<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Hydrator;

use App\Crm\Application\Model\BudgetStatisticModel;
use App\Crm\Application\Service\Invoice\InvoiceModel;

trait BudgetHydratorTrait
{
    protected function getBudgetValues(string $prefix, BudgetStatisticModel $statistic, InvoiceModel $model): array
    {
        $formatter = $model->getFormatter();
        $currency = $model->getCurrency();

        $budgetOpen = $statistic->getBudgetOpenRelative();
        $budgetTimeOpen = $statistic->getTimeBudgetOpenRelative();
        $budgetOpenDuration = $formatter->getFormattedDecimalDuration($budgetTimeOpen);

        return [
            $prefix . 'budget_open' => $formatter->getFormattedMoney($budgetOpen, $currency),
            $prefix . 'budget_open_plain' => $budgetOpen,
            $prefix . 'time_budget_open' => $budgetOpenDuration,
            $prefix . 'time_budget_open_plain' => $budgetTimeOpen,
        ];
    }
}
