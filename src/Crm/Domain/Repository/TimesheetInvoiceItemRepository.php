<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository;

use App\Crm\Application\Service\Invoice\InvoiceItemRepositoryInterface;
use App\Crm\Domain\Entity\ExportableItem;
use App\Crm\Domain\Entity\Timesheet;
use App\Crm\Domain\Repository\Query\InvoiceQuery;

final class TimesheetInvoiceItemRepository implements InvoiceItemRepositoryInterface
{
    public function __construct(
        private TimesheetRepository $repository
    ) {
    }

    /**
     * @return ExportableItem[]
     */
    public function getInvoiceItemsForQuery(InvoiceQuery $query): iterable
    {
        return $this->repository->getTimesheetsForQuery($query, true);
    }

    /**
     * @param ExportableItem[] $invoiceItems
     */
    public function setExported(array $invoiceItems): void
    {
        $timesheets = [];

        foreach ($invoiceItems as $item) {
            if ($item instanceof Timesheet) {
                $timesheets[] = $item;
            }
        }

        if (empty($timesheets)) {
            return;
        }

        $this->repository->setExported($timesheets);
    }
}
