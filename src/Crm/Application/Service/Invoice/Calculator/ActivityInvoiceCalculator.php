<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Calculator;

use App\Crm\Application\Service\Invoice\CalculatorInterface;
use App\Crm\Application\Service\Invoice\InvoiceItem;
use App\Crm\Domain\Entity\ExportableItem;

/**
 * A calculator that sums up the invoice item records by activity.
 */
final class ActivityInvoiceCalculator extends AbstractSumInvoiceCalculator implements CalculatorInterface
{
    public function getIdentifiers(ExportableItem $invoiceItem): array
    {
        return [
            $invoiceItem->getActivity()?->getId(),
        ];
    }

    public function getId(): string
    {
        return 'activity';
    }

    protected function mergeSumInvoiceItem(InvoiceItem $invoiceItem, ExportableItem $entry): void
    {
        if ($entry->getActivity() === null) {
            return;
        }

        if ($entry->getActivity()->getInvoiceText() !== null) {
            $invoiceItem->setDescription($entry->getActivity()->getInvoiceText());
        } else {
            $invoiceItem->setDescription($entry->getActivity()->getName());
        }
    }
}
