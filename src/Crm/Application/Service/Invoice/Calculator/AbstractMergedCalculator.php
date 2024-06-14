<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Calculator;

use App\Crm\Application\Service\Invoice\InvoiceItem;
use App\Crm\Domain\Entity\ExportableItem;
use App\Crm\Domain\Entity\Timesheet;

abstract class AbstractMergedCalculator extends AbstractCalculator
{
    public const TYPE_MIXED = 'mixed';
    public const CATEGORY_MIXED = 'mixed';

    protected function mergeInvoiceItems(InvoiceItem $invoiceItem, ExportableItem $entry): void
    {
        $duration = $invoiceItem->getDuration();
        if ($entry->getDuration() !== null) {
            $duration += $entry->getDuration();
        }

        $amount = $entry->getAmount();

        $type = $entry->getType();
        $category = $entry->getCategory();

        if ($invoiceItem->getType() !== null && $type !== $invoiceItem->getType()) {
            $type = self::TYPE_MIXED;
        }
        if ($invoiceItem->getCategory() !== null && $category !== $invoiceItem->getCategory()) {
            $category = self::CATEGORY_MIXED;
        }

        $invoiceItem->setType($type);
        $invoiceItem->setCategory($category);

        $invoiceItem->setAmount($invoiceItem->getAmount() + $amount);
        $invoiceItem->setUser($entry->getUser());
        $invoiceItem->setRate($invoiceItem->getRate() + $entry->getRate());
        $invoiceItem->setInternalRate($invoiceItem->getInternalRate() + ($entry->getInternalRate() ?? 0.00));
        $invoiceItem->setDuration($duration);

        if ($entry->getFixedRate() !== null) {
            /*
            if (null !== $invoiceItem->getFixedRate() && $invoiceItem->getFixedRate() !== $entry->getFixedRate()) {
                throw new \InvalidArgumentException('Cannot mix different fixed-rates');
            }
            */
            $invoiceItem->setFixedRate($entry->getFixedRate());
        }

        if ($entry->getHourlyRate() !== null) {
            /*
            if (null !== $invoiceItem->getHourlyRate() && $invoiceItem->getHourlyRate() !== $entry->getHourlyRate()) {
                throw new \InvalidArgumentException('Cannot mix different hourly-rates');
            }
            */
            $invoiceItem->setHourlyRate($entry->getHourlyRate());
        }

        if ($invoiceItem->getBegin() === null || $invoiceItem->getBegin()->getTimestamp() > $entry->getBegin()->getTimestamp()) {
            $invoiceItem->setBegin($entry->getBegin());
        }

        if ($invoiceItem->getEnd() === null || $invoiceItem->getEnd()->getTimestamp() < $entry->getEnd()->getTimestamp()) {
            $invoiceItem->setEnd($entry->getEnd());
        }

        if (!empty($entry->getDescription())) {
            $description = '';
            if (!empty($invoiceItem->getDescription())) {
                $description = $invoiceItem->getDescription() . PHP_EOL;
            }
            $invoiceItem->setDescription($description . $entry->getDescription());
        }

        if ($invoiceItem->getActivity() === null) {
            $invoiceItem->setActivity($entry->getActivity());
        }

        if ($invoiceItem->getProject() === null) {
            $invoiceItem->setProject($entry->getProject());
        }

        if (empty($invoiceItem->getDescription()) && $entry->getActivity() !== null) {
            $invoiceItem->setDescription($entry->getActivity()->getName());
        }

        if ($entry instanceof Timesheet) {
            foreach ($entry->getTagsAsArray() as $tag) {
                $invoiceItem->addTag($tag);
            }
        }
    }
}
