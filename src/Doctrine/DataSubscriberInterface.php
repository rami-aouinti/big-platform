<?php

declare(strict_types=1);

namespace App\Doctrine;

/**
 * Used to identify EventSubscribers, that work upon EntityManager events and listen on data changes.
 * These Subscribers will be deactivated on batch imports, for performance gains and reduced DB queries.
 */
interface DataSubscriberInterface
{
}
