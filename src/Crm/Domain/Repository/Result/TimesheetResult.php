<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Result;

use App\Crm\Application\Utils\Pagination;
use App\Crm\Domain\Entity\Timesheet;
use App\Crm\Domain\Repository\Loader\TimesheetLoader;
use App\Crm\Domain\Repository\Paginator\LoaderPaginator;
use App\Crm\Domain\Repository\Query\TimesheetQuery;
use Doctrine\ORM\QueryBuilder;

/**
 * @package App\Crm\Domain\Repository\Result
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class TimesheetResult
{
    private ?TimesheetResultStatistic $statisticCache = null;
    private bool $cachedFullyHydrated = false;
    /**
     * @var array<Timesheet>|null
     */
    private ?array $resultCache = null;

    /**
     * @internal
     */
    public function __construct(
        private readonly TimesheetQuery $query,
        private readonly QueryBuilder $queryBuilder
    ) {
    }

    public function getStatistic(): TimesheetResultStatistic
    {
        if ($this->statisticCache === null) {
            $withDuration = $this->query->countFilter() > 0;
            $qb = clone $this->queryBuilder;
            $qb
                ->resetDQLPart('select')
                ->resetDQLPart('orderBy')
                ->select('COUNT(t.id) as counter')
            ;

            if ($withDuration) {
                $qb->addSelect('COALESCE(SUM(t.duration), 0) as duration');
            }

            $result = $qb->getQuery()->getArrayResult()[0];
            $duration = $withDuration ? $result['duration'] : 0;

            $this->statisticCache = new TimesheetResultStatistic($result['counter'], (int)$duration);
        }

        return $this->statisticCache;
    }

    public function toIterable(): iterable
    {
        $query = $this->queryBuilder->getQuery();

        return $query->toIterable();
    }

    /**
     * @return array<Timesheet>
     */
    public function getResults(bool $fullyHydrated = false): array
    {
        if ($this->resultCache === null || ($fullyHydrated && $this->cachedFullyHydrated === false)) {
            $query = $this->queryBuilder->getQuery();
            $results = $query->getResult();

            $loader = new TimesheetLoader($this->queryBuilder->getEntityManager(), $fullyHydrated);
            $loader->loadResults($results);

            $this->cachedFullyHydrated = $fullyHydrated;
            $this->resultCache = $results;
        }

        return $this->resultCache;
    }

    public function getPagerfanta(bool $fullyHydrated = false): Pagination
    {
        $qb = clone $this->queryBuilder;

        $loader = new LoaderPaginator(new TimesheetLoader($qb->getEntityManager(), $fullyHydrated), $qb, $this->getStatistic()->getCount());
        $paginator = new Pagination($loader);
        $paginator->setMaxPerPage($this->query->getPageSize());
        $paginator->setCurrentPage($this->query->getPage());

        return $paginator;
    }
}
