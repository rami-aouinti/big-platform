<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber\Actions;

use App\Crm\Transport\Event\PageActionsEvent;

final class CustomersSubscriber extends AbstractActionsSubscriber
{
    public static function getActionName(): string
    {
        return 'customers';
    }

    public function onActions(PageActionsEvent $event): void
    {
        if ($this->isGranted('create_customer')) {
            $event->addCreate($this->path('admin_customer_create'));
        }

        $event->addQuickExport($this->path('customer_export'));
    }
}
