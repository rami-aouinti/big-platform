<?php

declare(strict_types=1);

namespace App\Pdf;

use App\Utils\FileHelper;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

trait PdfRendererTrait
{
    private bool $inline = false;

    public function setDispositionInline(bool $useInlineDisposition): void
    {
        $this->inline = $useInlineDisposition;
    }

    /**
     * @throws Exception
     */
    protected function createPdfResponse(string $content, PdfContext $context): Response
    {
        $filename = $context->getOption('filename');
        if (empty($filename)) {
            throw new Exception('Empty PDF filename given');
        }
        $filename = FileHelper::convertToAsciiFilename($filename);

        $response = new Response($content);

        $disposition = $this->inline ? ResponseHeaderBag::DISPOSITION_INLINE : ResponseHeaderBag::DISPOSITION_ATTACHMENT;
        $disposition = $response->headers->makeDisposition($disposition, $filename . '.pdf');

        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
