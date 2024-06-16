<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Entity\Invoice;
use App\Crm\Domain\Entity\InvoiceModel;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @package App\Crm\Transport\Event
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class InvoiceCreatedEvent extends Event
{
    public function __construct(
        private readonly Invoice $invoice,
        private readonly InvoiceModel $model
    ) {
    }

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    public function getInvoiceModel(): InvoiceModel
    {
        return $this->model;
    }
}
