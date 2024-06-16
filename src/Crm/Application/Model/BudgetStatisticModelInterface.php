<?php

declare(strict_types=1);

namespace App\Crm\Application\Model;

/**
 * @internal do not use in plugins, no BC promise given!
 */
interface BudgetStatisticModelInterface
{
    public function isMonthlyBudget(): bool;

    public function hasTimeBudget(): bool;

    public function getTimeBudget(): int;

    public function getTimeBudgetSpent(): int;

    public function hasBudget(): bool;

    public function getBudget(): float;

    public function getBudgetSpent(): float;
}
