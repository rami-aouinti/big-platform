<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Repository\Query\ExportQuery;
use Symfony\Contracts\EventDispatcher\Event;

final class ExportItemsQueryEvent extends Event
{
    public function __construct(
        private ExportQuery $query
    ) {
    }

    public function getExportQuery(): ExportQuery
    {
        return $this->query;
    }
}
