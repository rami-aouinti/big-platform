<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice;

use App\Crm\Application\Model\InvoiceDocument;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;

#[AutoconfigureTag]
interface RendererInterface
{
    /**
     * Checks whether the given InvoiceDocument can be rendered.
     */
    public function supports(InvoiceDocument $document): bool;

    /**
     * Render the given InvoiceDocument with the data from the InvoiceModel.
     */
    public function render(InvoiceDocument $document, InvoiceModel $model): Response;
}
