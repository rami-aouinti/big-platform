<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Repository\Query\ActivityQuery;

/**
 * Dynamically find possible meta fields for a activity query.
 *
 * @method ActivityQuery getQuery()
 */
final class ActivityMetaDisplayEvent extends AbstractMetaDisplayEvent
{
    public const EXPORT = 'export';
    public const ACTIVITY = 'activity';

    public function __construct(ActivityQuery $query, string $location)
    {
        parent::__construct($query, $location);
    }
}
