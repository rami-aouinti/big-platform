<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'platform_crm_customers_rates')]
#[ORM\UniqueConstraint(columns: ['user_id', 'customer_id'])]
#[ORM\Entity(repositoryClass: 'App\Crm\Domain\Repository\CustomerRateRepository')]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
#[UniqueEntity(['user', 'customer'], ignoreNull: false)]
#[Serializer\ExclusionPolicy('all')]
class CustomerRate implements RateInterface
{
    use Rate;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?Customer $customer = null;

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function getScore(): int
    {
        return 1;
    }
}
