<?php

declare(strict_types=1);

namespace App\Utils;

use App\Crm\Domain\Repository\Query\BaseQuery;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;

final class Pagination extends Pagerfanta
{
    public function __construct(AdapterInterface $adapter, ?BaseQuery $query = null)
    {
        parent::__construct($adapter);

        if ($query === null || !$query->isApiCall()) {
            $this->setNormalizeOutOfRangePages(true);
        }

        if ($query !== null) {
            $this->setMaxPerPage($query->getPageSize());
            $this->setCurrentPage($query->getPage());
        }
    }
}
