<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Repository\Query\CustomerQuery;

/**
 * Dynamically find possible meta fields for a customer query.
 *
 * @method CustomerQuery getQuery()
 */
final class CustomerMetaDisplayEvent extends AbstractMetaDisplayEvent
{
    public const EXPORT = 'export';
    public const CUSTOMER = 'customer';

    public function __construct(CustomerQuery $query, string $location)
    {
        parent::__construct($query, $location);
    }
}
