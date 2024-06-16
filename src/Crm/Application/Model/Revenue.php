<?php

declare(strict_types=1);

namespace App\Crm\Application\Model;

final class Revenue
{
    public function __construct(
        private readonly string $currency,
        private readonly float $amount
    ) {
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
