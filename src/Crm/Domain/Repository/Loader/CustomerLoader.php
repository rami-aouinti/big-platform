<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Loader;

use App\Crm\Domain\Entity\Customer;
use App\Crm\Domain\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;

final class CustomerLoader implements LoaderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private bool $fullyHydrated = false
    ) {
    }

    /**
     * @param array<int|Customer> $results
     */
    public function loadResults(array $results): void
    {
        if (empty($results)) {
            return;
        }

        $ids = array_map(function ($customer) {
            if ($customer instanceof Customer) {
                // make sure that this potential doctrine proxy is initialized and filled with all data
                $customer->getName();

                return $customer->getId();
            }

            return $customer;
        }, $results);

        $em = $this->entityManager;

        $qb = $em->createQueryBuilder();
        /** @var Customer[] $customers */
        $customers = $qb->select('PARTIAL c.{id}', 'meta')
            ->from(Customer::class, 'c')
            ->leftJoin('c.meta', 'meta')
            ->andWhere($qb->expr()->in('c.id', $ids))
            ->getQuery()
            ->execute();

        $qb = $em->createQueryBuilder();
        $qb->select('PARTIAL c.{id}', 'teams')
            ->from(Customer::class, 'c')
            ->leftJoin('c.teams', 'teams')
            ->andWhere($qb->expr()->in('c.id', $ids))
            ->getQuery()
            ->execute();

        // do not load team members or leads by default, because they will only be used on detail pages
        // and there is no benefit in adding multiple queries for most requests when they are only needed in one place
        if ($this->fullyHydrated) {
            $teamIds = [];
            foreach ($customers as $customer) {
                foreach ($customer->getTeams() as $team) {
                    $teamIds[] = $team->getId();
                }
            }
            $teamIds = array_unique($teamIds);

            if (\count($teamIds) > 0) {
                $qb = $em->createQueryBuilder();
                $qb->select('PARTIAL team.{id}', 'members', 'user')
                    ->from(Team::class, 'team')
                    ->leftJoin('team.members', 'members')
                    ->leftJoin('members.user', 'user')
                    ->andWhere($qb->expr()->in('team.id', $teamIds))
                    ->getQuery()
                    ->execute();
            }
        }
    }
}
