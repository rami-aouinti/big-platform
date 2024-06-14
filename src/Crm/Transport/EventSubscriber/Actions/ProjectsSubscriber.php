<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber\Actions;

use App\Crm\Transport\Event\PageActionsEvent;

final class ProjectsSubscriber extends AbstractActionsSubscriber
{
    public static function getActionName(): string
    {
        return 'projects';
    }

    public function onActions(PageActionsEvent $event): void
    {
        if ($this->isGranted('create_project')) {
            $event->addCreate($this->path('admin_project_create'));
        }

        $event->addQuickExport($this->path('project_export'));
    }
}
