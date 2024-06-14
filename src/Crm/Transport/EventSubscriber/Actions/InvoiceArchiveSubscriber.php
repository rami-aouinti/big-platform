<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber\Actions;

use App\Crm\Transport\Event\PageActionsEvent;

final class InvoiceArchiveSubscriber extends AbstractActionsSubscriber
{
    public static function getActionName(): string
    {
        return 'invoice_archive';
    }

    public function onActions(PageActionsEvent $event): void
    {
        $event->addQuickExport($this->path('invoice_export'));
    }
}
