<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Calculator;

use App\Crm\Application\Service\Invoice\CalculatorInterface;
use App\Crm\Application\Service\Invoice\InvoiceItem;

/**
 * Class DefaultCalculator works on all given entries using:
 * - the customer currency
 * - the invoice template vat rate
 * - the entries rate
 */
final class DefaultCalculator extends AbstractMergedCalculator implements CalculatorInterface
{
    /**
     * @return InvoiceItem[]
     */
    public function getEntries(): array
    {
        $entries = [];

        foreach ($this->model->getEntries() as $entry) {
            $item = new InvoiceItem();
            $this->mergeInvoiceItems($item, $entry);
            foreach ($entry->getMetaFields() as $field) {
                if ($field->getName() === null) {
                    continue;
                }
                $item->addAdditionalField($field->getName(), $field->getValue());
            }
            $entries[] = $item;
        }

        return $this->sortEntries($entries);
    }

    public function getId(): string
    {
        return 'default';
    }
}
