<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Entity\Customer;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Base event class to used with customer manipulations.
 */
abstract class AbstractCustomerEvent extends Event
{
    public function __construct(
        private Customer $customer
    ) {
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }
}
