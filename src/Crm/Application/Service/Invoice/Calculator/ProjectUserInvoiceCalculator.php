<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Calculator;

use App\Crm\Application\Service\Invoice\CalculatorInterface;
use App\Crm\Application\Service\Invoice\InvoiceItem;
use App\Crm\Domain\Entity\ExportableItem;

/**
 * A calculator that sums up the invoice item records by project and user.
 */
final class ProjectUserInvoiceCalculator extends AbstractSumInvoiceCalculator implements CalculatorInterface
{
    public function getIdentifiers(ExportableItem $invoiceItem): array
    {
        if ($invoiceItem->getProject() === null) {
            throw new \Exception('Cannot handle invoice items without project');
        }

        if ($invoiceItem->getProject()->getId() === null) {
            throw new \Exception('Cannot handle un-persisted projects');
        }

        if ($invoiceItem->getUser() === null) {
            throw new \Exception('Cannot handle invoice items without user');
        }

        if ($invoiceItem->getUser()->getId() === null) {
            throw new \Exception('Cannot handle un-persisted users');
        }

        return [
            $invoiceItem->getProject()->getId(),
            $invoiceItem->getUser()->getId(),
        ];
    }

    public function getId(): string
    {
        return 'project_user';
    }

    protected function mergeSumInvoiceItem(InvoiceItem $invoiceItem, ExportableItem $entry): void
    {
        if ($entry->getProject() === null) {
            return;
        }

        if ($entry->getProject()->getInvoiceText() !== null) {
            $invoiceItem->setDescription($entry->getProject()->getInvoiceText());
        } else {
            $invoiceItem->setDescription($entry->getProject()->getName());
        }
    }
}
