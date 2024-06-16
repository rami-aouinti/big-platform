<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'platform_crm_invoices_meta')]
#[ORM\UniqueConstraint(columns: ['invoice_id', 'name'])]
#[ORM\Entity]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
#[Serializer\ExclusionPolicy('all')]
class InvoiceMeta implements MetaTableTypeInterface
{
    use MetaTableTypeTrait;

    #[ORM\ManyToOne(targetEntity: Invoice::class, inversedBy: 'meta')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?Invoice $invoice = null;

    public function setEntity(EntityWithMetaFields $entity): MetaTableTypeInterface
    {
        if (!($entity instanceof Invoice)) {
            throw new \InvalidArgumentException(
                sprintf('Expected instanceof Invoice, received "%s"', \get_class($entity))
            );
        }
        $this->invoice = $entity;

        return $this;
    }

    /**
     * @return Invoice|null
     */
    public function getEntity(): ?EntityWithMetaFields
    {
        return $this->invoice;
    }
}
