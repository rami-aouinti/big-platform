<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository;

use App\Crm\Application\Utils\Pagination;
use App\Crm\Domain\Entity\Tag;
use App\Crm\Domain\Repository\Paginator\QueryBuilderPaginator;
use App\Crm\Domain\Repository\Query\TagFormTypeQuery;
use App\Crm\Domain\Repository\Query\TagQuery;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Exception;

/**
 * @extends EntityRepository<Tag>
 */
class TagRepository extends EntityRepository
{
    /**
     * See KimaiFormSelect.js (maxOptions) as well.
     */
    public const int MAX_AMOUNT_SELECT = 500;

    public function saveTag(Tag $tag): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($tag);
        $entityManager->flush();
    }

    public function deleteTag(Tag $tag): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($tag);
        $entityManager->flush();
    }

    /**
     * @param array<string> $tagNames
     * @return array<Tag>
     */
    public function findTagsByName(array $tagNames, ?bool $visible = null): array
    {
        if ($visible === null) {
            return $this->findBy([
                'name' => $tagNames,
            ]);
        }

        return $this->findBy([
            'name' => $tagNames,
            'visible' => $visible,
        ]);
    }

    public function findTagByName(string $tagName, ?bool $visible = null): ?Tag
    {
        if ($visible === null) {
            return $this->findOneBy([
                'name' => $tagName,
            ]);
        }

        return $this->findOneBy([
            'name' => $tagName,
            'visible' => $visible,
        ]);
    }

    /**
     * Find all visible tag names in alphabetical order.
     *
     * @return array<string>
     */
    public function findAllTagNames(?string $filter = null): array
    {
        $qb = $this->createQueryBuilder('t');

        $qb
            ->select('t.name')
            ->addOrderBy('t.name', 'ASC');

        $qb->andWhere($qb->expr()->eq('t.visible', ':visible'));
        $qb->setParameter('visible', true, ParameterType::BOOLEAN);

        if ($filter !== null) {
            $qb->andWhere('t.name LIKE :filter');
            $qb->setParameter('filter', '%' . $filter . '%');
        }

        return array_column($qb->getQuery()->getScalarResult(), 'name');
    }

    /**
     * Returns an array of arrays with each inner array having the structure:
     * - id
     * - name
     * - amount
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTagCount(TagQuery $query): Pagination
    {
        $qb = $this->getQueryBuilderForQuery($query);
        $qb1 = clone $qb;

        $qb
            ->resetDQLPart('select')
            ->resetDQLPart('orderBy')
            ->select($qb->expr()->count('tag.id'))
        ;
        $counter = (int)$qb->getQuery()->getSingleScalarResult();

        $paginator = new QueryBuilderPaginator($qb1, $counter);

        $pager = new Pagination($paginator);
        $pager->setMaxPerPage($query->getPageSize());
        $pager->setCurrentPage($query->getPage());

        return $pager;
    }

    public function getQueryBuilderForFormType(TagFormTypeQuery $query): QueryBuilder
    {
        $qb = $this->createQueryBuilder('tag');

        $qb->orderBy('tag.name', 'ASC');
        $qb->andWhere($qb->expr()->eq('tag.visible', ':visible'));
        $qb->setParameter('visible', true, ParameterType::BOOLEAN);

        return $qb;
    }

    /**
     * @param Tag[] $tags
     * @throws Exception
     */
    public function multiDelete(iterable $tags): void
    {
        $em = $this->getEntityManager();
        $em->beginTransaction();

        try {
            foreach ($tags as $tag) {
                $em->remove($tag);
            }
            $em->flush();
            $em->commit();
        } catch (Exception $ex) {
            $em->rollback();

            throw $ex;
        }
    }

    /**
     * @param Tag[] $tags
     * @throws Exception
     */
    public function multiUpdate(iterable $tags): void
    {
        $em = $this->getEntityManager();
        $em->beginTransaction();

        try {
            foreach ($tags as $tag) {
                $em->persist($tag);
            }
            $em->flush();
            $em->commit();
        } catch (Exception $ex) {
            $em->rollback();

            throw $ex;
        }
    }

    private function getQueryBuilderForQuery(TagQuery $query): QueryBuilder
    {
        $qb = $this->createQueryBuilder('tag');

        $qb->select('tag.id, tag.name, tag.color, tag.visible, SIZE(tag.timesheets) as amount');

        $orderBy = $query->getOrderBy();
        $orderBy = match ($orderBy) {
            'amount' => 'amount',
            default => 'tag.' . $orderBy,
        };

        if ($query->isShowVisible()) {
            $qb->andWhere($qb->expr()->eq('tag.visible', ':visible'));
            $qb->setParameter('visible', true, ParameterType::BOOLEAN);
        } elseif ($query->isShowHidden()) {
            $qb->andWhere($qb->expr()->eq('tag.visible', ':visible'));
            $qb->setParameter('visible', false, ParameterType::BOOLEAN);
        }

        $qb->addOrderBy($orderBy, $query->getOrder());

        if ($query->hasSearchTerm()) {
            $searchTerm = $query->getSearchTerm();
            $searchAnd = $qb->expr()->andX();

            if ($searchTerm->hasSearchTerm()) {
                $searchAnd->add(
                    $qb->expr()->orX(
                        $qb->expr()->like('tag.name', ':searchTerm')
                    )
                );
                $qb->setParameter('searchTerm', '%' . $searchTerm->getSearchTerm() . '%');
            }

            if ($searchAnd->count() > 0) {
                $qb->andWhere($searchAnd);
            }
        }

        return $qb;
    }
}
