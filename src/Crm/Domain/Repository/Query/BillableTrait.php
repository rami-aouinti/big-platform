<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Query;

trait BillableTrait
{
    private ?bool $billable = null;

    public function getBillable(): ?bool
    {
        return $this->billable;
    }

    public function isBillable(): bool
    {
        return $this->billable === true;
    }

    public function isNotBillable(): bool
    {
        return $this->billable === false;
    }

    public function isIgnoreBillable(): bool
    {
        return $this->billable === null;
    }

    public function setBillable(?bool $isBillable): void
    {
        $this->billable = $isBillable;
    }
}
