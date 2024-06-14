<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice;

/**
 * Interface InvoiceModelHydrator
 */
interface InvoiceModelHydrator
{
    public function hydrate(InvoiceModel $model): array;
}
