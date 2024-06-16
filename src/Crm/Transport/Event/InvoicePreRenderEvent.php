<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Application\Model\InvoiceDocument;
use App\Crm\Application\Service\Invoice\InvoiceModel;
use App\Crm\Application\Service\Invoice\RendererInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class InvoicePreRenderEvent extends Event
{
    public function __construct(
        private InvoiceModel $model,
        private InvoiceDocument $document,
        private RendererInterface $renderer
    ) {
    }

    public function getModel(): InvoiceModel
    {
        return $this->model;
    }

    public function getDocument(): InvoiceDocument
    {
        return $this->document;
    }

    public function getRenderer(): RendererInterface
    {
        return $this->renderer;
    }
}
