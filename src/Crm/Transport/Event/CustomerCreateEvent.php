<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

/**
 * Triggered for new customer instances, which might or might not be saved.
 */
final class CustomerCreateEvent extends AbstractCustomerEvent
{
}
