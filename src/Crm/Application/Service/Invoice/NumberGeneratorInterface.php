<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Class NumberGeneratorInterface defines all methods that invoice number generator have to implement.
 */
#[AutoconfigureTag]
interface NumberGeneratorInterface
{
    public function setModel(InvoiceModel $model): void;

    public function getInvoiceNumber(): string;

    /**
     * Returns the unique ID of this number generator.
     *
     * Prefix it with your company name followed by a hyphen (e.g. "acme-"),
     * if this is a third-party generator.
     */
    public function getId(): string;
}
