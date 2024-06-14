<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * CalculatorInterface defines all methods for any invoice price calculator.
 */
#[AutoconfigureTag]
interface CalculatorInterface
{
    /**
     * Return the invoice items that will be displayed on the invoice.
     *
     * @return InvoiceItem[]
     */
    public function getEntries(): array;

    /**
     * Set the invoice model and can be used to fetch the customer.
     */
    public function setModel(InvoiceModel $model): void;

    /**
     * Returns the subtotal before taxes.
     */
    public function getSubtotal(): float;

    /**
     * Returns the tax amount for this invoice.
     */
    public function getTax(): float;

    /**
     * Returns the total amount for this invoice including taxes.
     */
    public function getTotal(): float;

    /**
     * Returns the percentage for the value-added tax (VAT) calculation.
     */
    public function getVat(): float;

    /**
     * Returns the total amount of worked time in seconds.
     */
    public function getTimeWorked(): int;

    /**
     * Returns the unique ID of this calculator.
     *
     * Prefix it with your company name followed by a hyphen (e.g. "acme-"),
     * if this is a third-party calculator.
     */
    public function getId(): string;
}
