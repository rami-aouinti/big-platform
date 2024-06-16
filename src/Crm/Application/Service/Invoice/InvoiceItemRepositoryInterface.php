<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice;

use App\Crm\Domain\Entity\ExportableItem;
use App\Crm\Domain\Repository\Query\InvoiceQuery;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface InvoiceItemRepositoryInterface
{
    /**
     * @param ExportableItem[] $invoiceItems
     */
    public function setExported(array $invoiceItems) /* : void */;

    /**
     * @return ExportableItem[]
     */
    public function getInvoiceItemsForQuery(InvoiceQuery $query): iterable;
}
