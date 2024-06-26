<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber\Actions;

use App\Crm\Transport\Event\PageActionsEvent;

final class InvoiceTemplatesSubscriber extends AbstractActionsSubscriber
{
    public static function getActionName(): string
    {
        return 'invoice_templates';
    }

    public function onActions(PageActionsEvent $event): void
    {
        if ($this->isGranted('manage_invoice_template')) {
            $event->addAction('create', [
                'url' => $this->path('admin_invoice_template_create'),
                'class' => 'modal-ajax-form',
                'title' => 'create',
            ]);
        }

        // File upload does not work in a modal right now
        if ($this->isGranted('upload_invoice_template')) {
            $event->addAction('upload', [
                'url' => $this->path('admin_invoice_document_upload'),
                'title' => 'invoice_renderer',
                'translation_domain' => 'invoice-renderer',
            ]);
        }
    }
}
