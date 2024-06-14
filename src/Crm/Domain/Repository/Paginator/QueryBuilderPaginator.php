<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Paginator;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

final class QueryBuilderPaginator implements PaginatorInterface
{
    public function __construct(
        private QueryBuilder $query,
        private int $results
    ) {
    }

    public function getNbResults(): int
    {
        return $this->results;
    }

    /**
     * @return iterable<array-key, iterable<mixed>>
     */
    public function getSlice(int $offset, int $length): iterable
    {
        $query = $this->query
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($length);

        return $this->getResults($query);
    }

    public function getAll(): iterable
    {
        return $this->getResults($this->query->getQuery());
    }

    /**
     * @param Query<null, mixed> $query
     * @return iterable<array-key, iterable<mixed>>
     */
    private function getResults(Query $query)
    {
        return $query->execute(); // @phpstan-ignore-line
    }
}
