<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Loader;

use App\Crm\Application\Service\Invoice;
use Doctrine\ORM\EntityManagerInterface;

final class InvoiceLoader implements LoaderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @param array<int|Invoice> $results
     */
    public function loadResults(array $results): void
    {
        if (empty($results)) {
            return;
        }

        $ids = array_map(function ($invoice) {
            if ($invoice instanceof Invoice) {
                // make sure that this potential doctrine proxy is initialized and filled with all data
                $invoice->getInvoiceNumber();

                return $invoice->getId();
            }

            return $invoice;
        }, $results);

        $em = $this->entityManager;

        $qb = $em->createQueryBuilder();
        $qb->select('PARTIAL i.{id}', 'customer')
            ->from(Invoice::class, 'i')
            ->leftJoin('i.customer', 'customer')
            ->getQuery()
            ->execute();

        $qb = $em->createQueryBuilder();
        $qb->select('PARTIAL i.{id}', 'user')
            ->from(Invoice::class, 'i')
            ->leftJoin('i.user', 'user')
            ->getQuery()
            ->execute();

        $qb = $em->createQueryBuilder();
        $qb->select('PARTIAL i.{id}', 'meta')
            ->from(Invoice::class, 'i')
            ->leftJoin('i.meta', 'meta')
            ->andWhere($qb->expr()->in('i.id', $ids))
            ->getQuery()
            ->execute();
    }
}
