<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Calculator;

use App\Crm\Application\Service\Invoice\CalculatorInterface;
use App\Crm\Domain\Entity\ExportableItem;

/**
 * A calculator that sums up the invoice item records by price.
 */
final class PriceInvoiceCalculator extends AbstractSumInvoiceCalculator implements CalculatorInterface
{
    public function getIdentifiers(ExportableItem $invoiceItem): array
    {
        if ($invoiceItem->getFixedRate() !== null) {
            return ['fixed_' . $invoiceItem->getFixedRate()];
        }

        return ['hourly_' . $invoiceItem->getHourlyRate()];
    }

    public function getId(): string
    {
        return 'price';
    }
}
