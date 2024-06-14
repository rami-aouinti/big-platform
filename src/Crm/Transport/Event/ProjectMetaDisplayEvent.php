<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Repository\Query\ProjectQuery;

/**
 * Dynamically find possible meta fields for a project query.
 *
 * @method ProjectQuery getQuery()
 */
final class ProjectMetaDisplayEvent extends AbstractMetaDisplayEvent
{
    public const EXPORT = 'export';
    public const PROJECT = 'project';

    public function __construct(ProjectQuery $query, string $location)
    {
        parent::__construct($query, $location);
    }
}
