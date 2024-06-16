<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Calculator;

use App\Crm\Application\Service\Invoice\CalculatorInterface;
use App\Crm\Application\Service\Invoice\InvoiceItem;
use App\Crm\Domain\Entity\ExportableItem;

/**
 * An abstract calculator that sums up the invoice item records.
 */
abstract class AbstractSumInvoiceCalculator extends AbstractMergedCalculator implements CalculatorInterface
{
    /**
     * @return array<int|string|null>
     */
    public function getIdentifiers(ExportableItem $invoiceItem): array
    {
        return [];
    }

    /**
     * @return InvoiceItem[]
     */
    public function getEntries(): array
    {
        $entries = $this->model->getEntries();
        if (empty($entries)) {
            return [];
        }

        /** @var InvoiceItem[] $invoiceItems */
        $invoiceItems = [];

        foreach ($entries as $entry) {
            $id = $this->calculateIdentifier($entry);

            if (!isset($invoiceItems[$id])) {
                $invoiceItems[$id] = new InvoiceItem();
            }
            $invoiceItem = $invoiceItems[$id];
            $this->mergeInvoiceItems($invoiceItem, $entry);
            $this->mergeSumInvoiceItem($invoiceItem, $entry);
        }

        return $this->sortEntries(array_values($invoiceItems));
    }
    protected function calculateSumIdentifier(ExportableItem $invoiceItem): string
    {
        $ids = $this->getIdentifiers($invoiceItem);

        $identifier = '';
        foreach ($ids as $id) {
            if ($id === null) {
                $id = '__NULL__';
            }
            $identifier .= $id;
        }

        return $identifier;
    }

    protected function calculateIdentifier(ExportableItem $entry): string
    {
        $prefix = $this->calculateSumIdentifier($entry);

        if ($entry->getFixedRate() !== null) {
            return $prefix . '_fixed_' . (string)$entry->getFixedRate();
        }

        return $prefix . '_hourly_' . (string)$entry->getHourlyRate();
    }

    protected function mergeSumInvoiceItem(InvoiceItem $invoiceItem, ExportableItem $entry): void
    {
    }
}
