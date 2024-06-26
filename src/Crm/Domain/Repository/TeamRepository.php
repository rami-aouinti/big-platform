<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository;

use App\Crm\Application\Utils\Pagination;
use App\Crm\Domain\Entity\Team;
use App\Crm\Domain\Entity\TeamMember;
use App\Crm\Domain\Entity\Timesheet;
use App\Crm\Domain\Repository\Loader\TeamLoader;
use App\Crm\Domain\Repository\Paginator\LoaderPaginator;
use App\Crm\Domain\Repository\Paginator\PaginatorInterface;
use App\Crm\Domain\Repository\Query\TeamQuery;
use App\User\Domain\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends EntityRepository<Team>
 */
class TeamRepository extends EntityRepository
{
    /**
     * @return Team[]
     */
    public function findAll(): array
    {
        $result = parent::findAll();

        $loader = new TeamLoader($this->getEntityManager());
        $loader->loadResults($result);

        return $result;
    }

    /**
     * @param int[] $teamIds
     * @return Team[]
     */
    public function findByIds(array $teamIds): array
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where($qb->expr()->in('t.id', ':id'))
            ->setParameter('id', $teamIds)
        ;

        $teams = $qb->getQuery()->getResult();

        $loader = new TeamLoader($qb->getEntityManager());
        $loader->loadResults($teams);

        return $teams;
    }

    public function saveTeam(Team $team): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($team);
        $entityManager->flush();
    }

    public function removeTeamMember(TeamMember $member): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($member);
    }

    public function deleteTeam(Team $team): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($team);
        $entityManager->flush();
    }

    /**
     * Returns a query builder that is used for TeamType and your own 'query_builder' option.
     */
    public function getQueryBuilderForFormType(TeamQuery $query): QueryBuilder
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('t')
            ->from(Team::class, 't')
            ->orderBy('t.name', 'ASC');

        $this->addPermissionCriteria($qb, $query->getCurrentUser(), $query->getTeams());

        return $qb;
    }

    public function getPagerfantaForQuery(TeamQuery $query): Pagination
    {
        return new Pagination($this->getPaginatorForQuery($query), $query);
    }

    /**
     * @return Timesheet[]
     */
    public function getTeamsForQuery(TeamQuery $query): iterable
    {
        // this is using the paginator internally, as it will load all joined entities into the working unit
        // do not "optimize" to use the query directly, as it would results in hundreds of additional lazy queries
        $paginator = $this->getPaginatorForQuery($query);

        return $paginator->getAll();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    protected function getPaginatorForQuery(TeamQuery $query): PaginatorInterface
    {
        $qb = $this->getQueryBuilderForQuery($query);
        $qb
            ->resetDQLPart('select')
            ->resetDQLPart('orderBy')
            ->select($qb->expr()->countDistinct('t.id'))
        ;
        $counter = (int)$qb->getQuery()->getSingleScalarResult();

        $qb = $this->getQueryBuilderForQuery($query);

        return new LoaderPaginator(new TeamLoader($qb->getEntityManager()), $qb, $counter);
    }

    private function getQueryBuilderForQuery(TeamQuery $query): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t');

        $qb->select('t');

        $orderBy = $query->getOrderBy();
        $orderBy = 't.' . $orderBy;

        if ($query->hasCustomers()) {
            $qb->leftJoin('t.customers', 'qCustomers');
            $qb->orWhere(
                $qb->expr()->in('qCustomers', ':customers')
            );
            $qb->setParameter('customers', $query->getCustomers());
        }

        if ($query->hasProjects()) {
            $qb->leftJoin('t.projects', 'qProjects');
            $qb->orWhere(
                $qb->expr()->in('qProjects', ':projects')
            );
            $qb->setParameter('projects', $query->getProjects());
        }

        if ($query->hasActivities()) {
            $qb->leftJoin('t.activities', 'qActivities');
            $qb->orWhere(
                $qb->expr()->in('qActivities', ':activities')
            );
            $qb->setParameter('activities', $query->getActivities());
        }

        if ($query->hasUsers()) {
            $qb->leftJoin('t.members', 'qMembers');
            $qb->orWhere(
                $qb->expr()->in('qMembers.user', ':user')
            );
            $qb->setParameter('user', $query->getUsers());
        }

        $qb->addOrderBy($orderBy, $query->getOrder());

        if (!empty($query->getSearchTerm())) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('t.name', ':likeContains')
                )
            );
            $qb->setParameter('likeContains', '%' . $query->getSearchTerm() . '%');
        }

        $this->addPermissionCriteria($qb, $query->getCurrentUser(), $query->getTeams());

        return $qb;
    }

    /**
     * @param Team[] $teams
     */
    private function addPermissionCriteria(QueryBuilder $qb, ?User $user = null, array $teams = []): void
    {
        // make sure that all queries without a user see all user
        if ($user === null && empty($teams)) {
            return;
        }

        // make sure that admins see all user
        if ($user !== null && $user->canSeeAllData()) {
            return;
        }

        // this is an OR on purpose because we either query only for teams where the user is teamlead
        // OR we query for all teams where the user is a member - in later case $teams is not empty
        $or = $qb->expr()->orX();

        // this query should limit to teams where the user is a teamlead (eg. in dropdowns or listing page)
        if ($user !== null) {
            $qb->leftJoin('t.members', 'members');
            $or->add(
                $qb->expr()->andX(
                    $qb->expr()->eq('members.user', ':id'),
                    $qb->expr()->eq('members.teamlead', true)
                )
            );
            $qb->setParameter('id', $user);
        }

        // this is primarily used, if we want to query for teams of the current user
        // and not 'teamlead_only' as used in the teams form type
        if (!empty($teams)) {
            $ids = [];
            foreach ($teams as $team) {
                $ids[] = $team->getId();
            }
            $or->add($qb->expr()->in('t.id', ':teamIds'));
            $qb->setParameter('teamIds', array_unique($ids));
        }

        if ($or->count() > 0) {
            $qb->andWhere($or);
        }
    }
}
