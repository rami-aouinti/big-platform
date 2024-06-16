<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice;

interface InvoiceItemHydrator
{
    public function setInvoiceModel(InvoiceModel $model): void;

    public function hydrate(InvoiceItem $item): array;
}
