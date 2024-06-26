<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Application\Model\InvoiceDocument;
use Symfony\Contracts\EventDispatcher\Event;

final class InvoiceDocumentsEvent extends Event
{
    /**
     * Maximum amount of allowed invoice documents.
     */
    private int $maximum = 99;

    /**
     * @param InvoiceDocument[] $documents
     */
    public function __construct(
        private array $documents
    ) {
    }

    /**
     * @return InvoiceDocument[]
     */
    public function getInvoiceDocuments(): array
    {
        return $this->documents;
    }

    public function addInvoiceDocuments(InvoiceDocument $document): void
    {
        $this->documents[] = $document;
    }

    /**
     * @param InvoiceDocument[] $documents
     */
    public function setInvoiceDocuments(array $documents): void
    {
        $this->documents = $documents;
    }

    public function setMaximumAllowedDocuments(int $max): void
    {
        $this->maximum = $max;
    }

    public function getMaximumAllowedDocuments(): int
    {
        return $this->maximum;
    }
}
