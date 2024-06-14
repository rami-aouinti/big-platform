<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository;

use App\Crm\Application\Service\Invoice\Customer;
use App\Crm\Application\Service\Invoice\CustomerRate;
use Doctrine\ORM\EntityRepository;

/**
 * @extends \Doctrine\ORM\EntityRepository<CustomerRate>
 */
class CustomerRateRepository extends EntityRepository
{
    public function saveRate(CustomerRate $rate): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($rate);
        $entityManager->flush();
    }

    public function deleteRate(CustomerRate $rate): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($rate);
        $entityManager->flush();
    }

    /**
     * @return CustomerRate[]
     */
    public function getRatesForCustomer(Customer $customer): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('r, u, c')
            ->from(CustomerRate::class, 'r')
            ->leftJoin('r.user', 'u')
            ->leftJoin('r.customer', 'c')
            ->andWhere(
                $qb->expr()->eq('r.customer', ':customer')
            )
            ->addOrderBy('u.alias')
            ->setParameter('customer', $customer)
        ;

        return $qb->getQuery()->getResult();
    }
}
