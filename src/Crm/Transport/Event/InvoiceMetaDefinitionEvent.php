<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event can be used, to dynamically add meta fields to invoices
 */
final class InvoiceMetaDefinitionEvent extends Event
{
    public function __construct(
        private Invoice $entity
    ) {
    }

    public function getEntity(): Invoice
    {
        return $this->entity;
    }
}
