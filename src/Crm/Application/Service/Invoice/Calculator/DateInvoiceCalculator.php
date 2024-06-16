<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Calculator;

use App\Crm\Application\Service\Invoice\CalculatorInterface;
use App\Crm\Domain\Entity\ExportableItem;

/**
 * A calculator that sums up the invoice item records for each day.
 */
final class DateInvoiceCalculator extends AbstractSumInvoiceCalculator implements CalculatorInterface
{
    public function getIdentifiers(ExportableItem $invoiceItem): array
    {
        if ($invoiceItem->getBegin() === null) {
            throw new \Exception('Cannot handle invoice items without start date');
        }

        return [
            $invoiceItem->getBegin()->format('Y-m-d'),
        ];
    }

    public function getId(): string
    {
        return 'date';
    }
}
