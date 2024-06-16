<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'platform_crm_customers_meta')]
#[ORM\UniqueConstraint(columns: ['customer_id', 'name'])]
#[ORM\Entity]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
#[Serializer\ExclusionPolicy('all')]
class CustomerMeta implements MetaTableTypeInterface
{
    use MetaTableTypeTrait;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'meta')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?Customer $customer = null;

    public function setEntity(EntityWithMetaFields $entity): MetaTableTypeInterface
    {
        if (!($entity instanceof Customer)) {
            throw new \InvalidArgumentException(
                sprintf('Expected instanceof Customer, received "%s"', \get_class($entity))
            );
        }
        $this->customer = $entity;

        return $this;
    }

    /**
     * @return Customer|null
     */
    public function getEntity(): ?EntityWithMetaFields
    {
        return $this->customer;
    }
}
