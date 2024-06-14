<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice;

/**
 * @package App\Crm\Application\Service\Invoice
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class DuplicateInvoiceNumberException extends \Exception
{
    public function __construct(string $invoiceNumber)
    {
        parent::__construct('Invoice number "' . $invoiceNumber . '" already existing');
    }
}
