<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Entity\Invoice;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @package App\Crm\Transport\Event
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class InvoiceDeleteEvent extends Event
{
    public function __construct(
        private readonly Invoice $invoice
    ) {
    }

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }
}
