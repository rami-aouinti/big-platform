<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Calculator;

use App\Crm\Application\Service\Invoice\CalculatorInterface;
use App\Crm\Domain\Entity\ExportableItem;

/**
 * A calculator that sums up the invoice item records by user.
 */
final class UserInvoiceCalculator extends AbstractSumInvoiceCalculator implements CalculatorInterface
{
    public function getIdentifiers(ExportableItem $invoiceItem): array
    {
        if ($invoiceItem->getUser()?->getId() === null) {
            throw new \Exception('Cannot handle un-persisted user');
        }

        return [
            $invoiceItem->getUser()->getId(),
        ];
    }

    public function getId(): string
    {
        return 'user';
    }
}
