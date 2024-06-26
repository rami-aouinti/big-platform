<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Renderer;

use App\Crm\Application\Model\InvoiceDocument;
use App\Crm\Application\Service\Invoice\InvoiceModel;
use Symfony\Component\HttpFoundation\Response;

final class TwigRenderer extends AbstractTwigRenderer
{
    public function supports(InvoiceDocument $document): bool
    {
        return stripos($document->getFilename(), '.html.twig') !== false;
    }

    public function render(InvoiceDocument $document, InvoiceModel $model): Response
    {
        $content = $this->renderTwigTemplate($document, $model);

        $response = new Response();
        $response->setContent($content);
        $response->headers->set('Content-Type', 'text/html; charset=UTF-8');

        return $response;
    }
}
