<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Paginator;

use Pagerfanta\Adapter\AdapterInterface;

interface PaginatorInterface extends AdapterInterface
{
    /**
     * Returns all available results without pagination.
     */
    public function getAll(): iterable;
}
