<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository;

use App\Crm\Application\Service\Invoice\InvoiceTemplate;
use App\Crm\Application\Utils\Pagination;
use App\Crm\Domain\Repository\Paginator\PaginatorInterface;
use App\Crm\Domain\Repository\Paginator\QueryBuilderPaginator;
use App\Crm\Domain\Repository\Query\BaseQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends \Doctrine\ORM\EntityRepository<InvoiceTemplate>
 */
class InvoiceTemplateRepository extends EntityRepository
{
    public function hasTemplate(): bool
    {
        return $this->count([]) > 0;
    }

    public function getQueryBuilderForFormType(): QueryBuilder
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('t')
            ->from(InvoiceTemplate::class, 't')
            ->orderBy('t.name');

        return $qb;
    }

    public function getPagerfantaForQuery(BaseQuery $query): Pagination
    {
        return new Pagination($this->getPaginatorForQuery($query), $query);
    }

    public function countTemplatesForQuery(BaseQuery $query): int
    {
        $qb = $this->getQueryBuilderForQuery($query);
        $qb
            ->resetDQLPart('select')
            ->resetDQLPart('orderBy')
            ->select($qb->expr()->countDistinct('t.id'))
        ;

        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    public function saveTemplate(InvoiceTemplate $template): void
    {
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
    }

    public function removeTemplate(InvoiceTemplate $template): void
    {
        $this->getEntityManager()->remove($template);
        $this->getEntityManager()->flush();
    }

    protected function getPaginatorForQuery(BaseQuery $query): PaginatorInterface
    {
        $counter = $this->countTemplatesForQuery($query);
        $qb = $this->getQueryBuilderForQuery($query);

        return new QueryBuilderPaginator($qb, $counter);
    }

    private function getQueryBuilderForQuery(BaseQuery $query): QueryBuilder
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('t')
            ->from(InvoiceTemplate::class, 't')
            ->orderBy('t.name');

        return $qb;
    }
}
