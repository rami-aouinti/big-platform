<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber\Actions;

use App\Crm\Transport\Event\PageActionsEvent;

final class ActivitiesSubscriber extends AbstractActionsSubscriber
{
    public static function getActionName(): string
    {
        return 'activities';
    }

    public function onActions(PageActionsEvent $event): void
    {
        if ($this->isGranted('create_activity')) {
            $event->addCreate($this->path('admin_activity_create'));
        }

        $event->addQuickExport($this->path('activity_export'));
    }
}
