<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository;

use App\Crm\Domain\Entity\WorkingTime;
use App\User\Domain\Entity\User;
use DateTimeInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\QueryException;

/**
 * @extends EntityRepository<WorkingTime>
 * @internal use WorkingTimeService instead!
 */
class WorkingTimeRepository extends EntityRepository
{
    private bool $pendingUpdate = false;

    public function deleteWorkingTime(WorkingTime $workingTime): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($workingTime);
        $entityManager->flush();
    }

    public function saveWorkingTime(WorkingTime $workingTime): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($workingTime);
        $entityManager->flush();
    }

    public function scheduleWorkingTimeUpdate(WorkingTime $workingTime): void
    {
        $this->pendingUpdate = true;
        $this->getEntityManager()->persist($workingTime);
    }

    public function persistScheduledWorkingTimes(): void
    {
        if ($this->pendingUpdate) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws QueryException
     * @return array<WorkingTime>
     */
    public function findForYear(User $user, DateTimeInterface $year): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->select('w')
            ->where($qb->expr()->eq('w.user', ':user'))
            ->setParameter('user', $user->getId())
            ->andWhere($qb->expr()->eq('YEAR(w.date)', ':date'))
            ->setParameter('date', $year->format('Y'))
            ->indexBy('w', 'w.date')
            ->orderBy('w.date')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getLatestApproval(User $user): ?WorkingTime
    {
        $qb = $this->createQueryBuilder('w');
        $qb->select('MAX(DATE(w.date))')
            ->where($qb->expr()->eq('w.user', ':user'))
            ->setParameter('user', $user->getId())
            ->andWhere($qb->expr()->isNotNull('w.approvedAt'))
        ;

        $date = $qb->getQuery()->getSingleScalarResult();

        if ($date === null) {
            return null;
        }

        $qb = $this->createQueryBuilder('w');
        $qb->select('w')
            ->where($qb->expr()->eq('w.user', ':user'))
            ->setParameter('user', $user->getId())
            ->andWhere($qb->expr()->eq('DATE(w.date)', 'DATE(:date)'))
            ->setParameter('date', $date)
        ;

        return $qb->getQuery()->getOneOrNullResult(); // @phpstan-ignore-line
    }
}
