<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

/**
 * @internal
 */
interface EntityWithBudget
{
    public function setBudget(float $budget): void;

    public function getBudget(): float;

    public function hasBudget(): bool;

    public function setTimeBudget(int $seconds): void;

    public function getTimeBudget(): int;

    public function hasTimeBudget(): bool;

    public function isMonthlyBudget(): bool;

    public function getBudgetType(): ?string;

    public function setBudgetType(?string $budgetType = null): void;
}
