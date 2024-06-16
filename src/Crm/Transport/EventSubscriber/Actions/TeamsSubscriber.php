<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber\Actions;

use App\Crm\Transport\Event\PageActionsEvent;

final class TeamsSubscriber extends AbstractActionsSubscriber
{
    public static function getActionName(): string
    {
        return 'teams';
    }

    public function onActions(PageActionsEvent $event): void
    {
        if ($this->isGranted('create_team')) {
            $event->addCreate($this->path('admin_team_create'));
        }
    }
}
