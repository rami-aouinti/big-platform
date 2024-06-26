<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Renderer;

use App\Crm\Application\Model\InvoiceDocument;
use App\Crm\Application\Service\Invoice\InvoiceFilename;
use App\Crm\Application\Service\Invoice\InvoiceModel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @internal
 */
abstract class AbstractRenderer
{
    public function supports(InvoiceDocument $document): bool
    {
        foreach ($this->getFileExtensions() as $extension) {
            if (stripos($document->getFilename(), $extension) !== false) {
                return true;
            }
        }

        return false;
    }
    /**
     * @return string[]
     */
    abstract protected function getFileExtensions(): array;

    abstract protected function getContentType(): string;

    protected function buildFilename(InvoiceModel $model): string
    {
        return (string)new InvoiceFilename($model);
    }

    protected function getFileResponse(mixed $file, string $filename): BinaryFileResponse
    {
        $response = new BinaryFileResponse($file);
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);

        $response->headers->set('Content-Type', $this->getContentType());
        $response->headers->set('Content-Disposition', $disposition);
        $response->deleteFileAfterSend(true);

        return $response;
    }
}
