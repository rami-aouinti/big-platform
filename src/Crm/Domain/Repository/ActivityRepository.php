<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository;

use App\Crm\Application\Utils\Pagination;
use App\Crm\Domain\Entity\Activity;
use App\Crm\Domain\Entity\ActivityMeta;
use App\Crm\Domain\Entity\Project;
use App\Crm\Domain\Entity\Team;
use App\Crm\Domain\Entity\Timesheet;
use App\Crm\Domain\Repository\Loader\ActivityLoader;
use App\Crm\Domain\Repository\Paginator\LoaderPaginator;
use App\Crm\Domain\Repository\Paginator\PaginatorInterface;
use App\Crm\Domain\Repository\Query\ActivityFormTypeQuery;
use App\Crm\Domain\Repository\Query\ActivityQuery;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\QueryBuilder;

use function count;

/**
 * @extends EntityRepository<Activity>
 */
class ActivityRepository extends EntityRepository
{
    use RepositorySearchTrait;

    /**
     * @return Activity[]
     */
    public function findByProject(Project $project): array
    {
        return $this->findBy([
            'project' => $project,
        ]);
    }

    /**
     * @param int[] $activityIds
     * @return Activity[]
     */
    public function findByIds(array $activityIds): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->where($qb->expr()->in('a.id', ':id'))
            ->setParameter('id', $activityIds)
        ;

        $activities = $qb->getQuery()->getResult();

        $loader = new ActivityLoader($qb->getEntityManager(), true);
        $loader->loadResults($activities);

        return $activities;
    }

    public function saveActivity(Activity $activity): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($activity);
        $entityManager->flush();
    }

    public function countActivity(bool $visible = null): int
    {
        if ($visible !== null) {
            return $this->count([
                'visible' => (bool)$visible,
            ]);
        }

        return $this->count([]);
    }

    /**
     * Returns a query builder that is used for ActivityType and your own 'query_builder' option.
     */
    public function getQueryBuilderForFormType(ActivityFormTypeQuery $query): QueryBuilder
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('a')
            ->from(Activity::class, 'a')
            ->addOrderBy('a.project', 'DESC')
            ->addOrderBy('a.name', 'ASC')
        ;

        $mainQuery = $qb->expr()->andX();

        $mainQuery->add($qb->expr()->eq('a.visible', ':visible'));
        $qb->setParameter('visible', true, ParameterType::BOOLEAN);

        if (!$query->isGlobalsOnly()) {
            $qb
                ->addSelect('p')
                ->addSelect('c')
                ->leftJoin('a.project', 'p')
                ->leftJoin('p.customer', 'c');

            $mainQuery->add(
                $qb->expr()->orX(
                    $qb->expr()->isNull('a.project'),
                    $qb->expr()->andX(
                        $qb->expr()->eq('p.visible', ':is_visible'),
                        $qb->expr()->eq('c.visible', ':is_visible')
                    )
                )
            );

            $qb->setParameter('is_visible', true, ParameterType::BOOLEAN);
        }

        if ($query->isGlobalsOnly()) {
            $mainQuery->add($qb->expr()->isNull('a.project'));
        } elseif ($query->hasProjects()) {
            $orX = $qb->expr()->orX(
                $qb->expr()->in('a.project', ':project')
            );

            $includeGlobals = true;
            // projects have a setting to disallow global activities, and we check for it only
            // if we query for exactly one project (usually used in dropdown queries)
            if (count($query->getProjects()) === 1) {
                $project = $query->getProjects()[0];
                if (!$project instanceof Project) {
                    $project = $this->getEntityManager()->getRepository(Project::class)->find($project);
                }
                if ($project instanceof Project) {
                    $includeGlobals = $project->isGlobalActivities();
                }
            }

            if ($includeGlobals) {
                $orX->add($qb->expr()->isNull('a.project'));
            }

            $mainQuery->add($orX);
            $qb->setParameter('project', $query->getProjects());
        }

        $permissions = $this->getPermissionCriteria($qb, $query->getUser(), $query->getTeams(), $query->isGlobalsOnly());
        if ($permissions->count() > 0) {
            $mainQuery->add($permissions);
        }

        $outerQuery = $qb->expr()->orX();

        if ($query->hasActivities()) {
            $outerQuery->add($qb->expr()->in('a.id', ':activity'));
            $qb->setParameter('activity', $query->getActivities());
        }

        if ($query->getActivityToIgnore() !== null) {
            $mainQuery = $qb->expr()->andX(
                $mainQuery,
                $qb->expr()->neq('a.id', ':ignored')
            );
            $qb->setParameter('ignored', $query->getActivityToIgnore());
        }

        $outerQuery->add($mainQuery);

        $qb->andWhere($outerQuery);

        return $qb;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countActivitiesForQuery(ActivityQuery $query): int
    {
        $qb = $this->getQueryBuilderForQuery($query);
        $qb
            ->resetDQLPart('select')
            ->resetDQLPart('orderBy')
            ->resetDQLPart('groupBy')
            ->select($qb->expr()->countDistinct('a.id'))
        ;

        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    public function getPagerfantaForQuery(ActivityQuery $query): Pagination
    {
        return new Pagination($this->getPaginatorForQuery($query), $query);
    }

    /**
     * @return Activity[]
     */
    public function getActivitiesForQuery(ActivityQuery $query): iterable
    {
        // this is using the paginator internally, as it will load all joined entities into the working unit
        // do not "optimize" to use the query directly, as it would results in hundreds of additional lazy queries
        $paginator = $this->getPaginatorForQuery($query);

        return $paginator->getAll();
    }

    /**
     * @throws ORMException
     */
    public function deleteActivity(Activity $delete, ?Activity $replace = null): void
    {
        $em = $this->getEntityManager();
        $em->beginTransaction();

        try {
            if ($replace !== null) {
                $qb = $em->createQueryBuilder();
                $qb->update(Timesheet::class, 't')
                    ->set('t.activity', ':replace')
                    ->where('t.activity = :delete')
                    ->setParameter('delete', $delete)
                    ->setParameter('replace', $replace);

                $qb->getQuery()->execute();
            }

            $em->remove($delete);
            $em->flush();
            $em->commit();
        } catch (ORMException $ex) {
            $em->rollback();

            throw $ex;
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    protected function getPaginatorForQuery(ActivityQuery $query): PaginatorInterface
    {
        $counter = $this->countActivitiesForQuery($query);
        $qb = $this->getQueryBuilderForQuery($query);

        return new LoaderPaginator(new ActivityLoader($qb->getEntityManager()), $qb, $counter);
    }

    private function addPermissionCriteria(QueryBuilder $qb, ?User $user = null, array $teams = [], bool $globalsOnly = false): void
    {
        $permissions = $this->getPermissionCriteria($qb, $user, $teams, $globalsOnly);
        if ($permissions->count() > 0) {
            $qb->andWhere($permissions);
        }
    }

    private function getPermissionCriteria(QueryBuilder $qb, ?User $user = null, array $teams = [], bool $globalsOnly = false): Andx
    {
        $andX = $qb->expr()->andX();

        // make sure that all queries without a user see all projects
        if ($user === null && empty($teams)) {
            return $andX;
        }

        // make sure that admins see all activities
        if ($user !== null && $user->canSeeAllData()) {
            return $andX;
        }

        if ($user !== null) {
            $teams = array_merge($teams, $user->getTeams());
        }

        if (empty($teams)) {
            $andX->add('SIZE(a.teams) = 0');
            if (!$globalsOnly) {
                $andX->add('SIZE(p.teams) = 0');
                $andX->add('SIZE(c.teams) = 0');
            }

            return $andX;
        }

        $orActivity = $qb->expr()->orX(
            'SIZE(a.teams) = 0',
            $qb->expr()->isMemberOf(':teams', 'a.teams')
        );
        $andX->add($orActivity);

        if (!$globalsOnly) {
            $orProject = $qb->expr()->orX(
                'SIZE(p.teams) = 0',
                $qb->expr()->isMemberOf(':teams', 'p.teams')
            );
            $andX->add($orProject);

            $orCustomer = $qb->expr()->orX(
                'SIZE(c.teams) = 0',
                $qb->expr()->isMemberOf(':teams', 'c.teams')
            );
            $andX->add($orCustomer);
        }

        $ids = array_values(array_unique(array_map(function (Team $team) {
            return $team->getId();
        }, $teams)));

        $qb->setParameter('teams', $ids);

        return $andX;
    }

    /**
     * @throws RepositoryException
     */
    private function getQueryBuilderForQuery(ActivityQuery $query): QueryBuilder
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('a')
            ->from(Activity::class, 'a')
            ->leftJoin('a.project', 'p')
            ->leftJoin('p.customer', 'c')
        ;

        foreach ($query->getOrderGroups() as $orderBy => $order) {
            switch ($orderBy) {
                case 'project':
                    $orderBy = 'p.name';
                    break;
                case 'customer':
                    $orderBy = 'c.name';
                    break;
                default:
                    $orderBy = 'a.' . $orderBy;
                    break;
            }
            $qb->addOrderBy($orderBy, $order);
        }

        $where = $qb->expr()->andX();

        if (!$query->isShowBoth()) {
            $where->add($qb->expr()->eq('a.visible', ':visible'));

            if (!$query->isGlobalsOnly()) {
                $where->add(
                    $qb->expr()->orX(
                        $qb->expr()->isNull('a.project'),
                        $qb->expr()->andX(
                            $qb->expr()->eq('p.visible', ':is_visible'),
                            $qb->expr()->eq('c.visible', ':is_visible')
                        )
                    )
                );
                $qb->setParameter('is_visible', true, ParameterType::BOOLEAN);
            }

            if ($query->isShowVisible()) {
                $qb->setParameter('visible', true, ParameterType::BOOLEAN);
            } elseif ($query->isShowHidden()) {
                $qb->setParameter('visible', false, ParameterType::BOOLEAN);
            }
        }

        if ($query->isGlobalsOnly()) {
            $where->add($qb->expr()->isNull('a.project'));
        } elseif ($query->hasProjects()) {
            $orX = $qb->expr()->orX(
                $qb->expr()->in('a.project', ':project')
            );

            if (!$query->isExcludeGlobals()) {
                $includeGlobals = true;
                // projects have a setting to disallow global activities, and we check for it only
                // if we query for exactly one project (usually used in dropdown queries)
                if (count($query->getProjects()) === 1) {
                    $includeGlobals = $query->getProjects()[0]->isGlobalActivities();
                }
                if ($includeGlobals) {
                    $orX->add($qb->expr()->isNull('a.project'));
                }
            }

            $where->add($orX);
            $qb->setParameter('project', $query->getProjectIds());
        } elseif ($query->hasCustomers()) {
            $where->add($qb->expr()->in('p.customer', ':customer'));
            $qb->setParameter('customer', $query->getCustomerIds());
        }

        if ($where->count() > 0) {
            $qb->andWhere($where);
        }

        $this->addPermissionCriteria($qb, $query->getCurrentUser(), $query->getTeams(), $query->isGlobalsOnly());

        $this->addSearchTerm($qb, $query);

        return $qb;
    }

    private function getMetaFieldClass(): string
    {
        return ActivityMeta::class;
    }

    private function getMetaFieldName(): string
    {
        return 'activity';
    }

    /**
     * @return array<string>
     */
    private function getSearchableFields(): array
    {
        return ['a.name', 'a.comment', 'a.number'];
    }
}
