<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Entity\Customer;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event can be used, to dynamically add meta fields to customers
 */
final class CustomerMetaDefinitionEvent extends Event
{
    public function __construct(
        private Customer $entity
    ) {
    }

    public function getEntity(): Customer
    {
        return $this->entity;
    }
}
