<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber\Actions;

use App\Crm\Transport\Event\PageActionsEvent;

final class PermissionsSubscriber extends AbstractActionsSubscriber
{
    public static function getActionName(): string
    {
        return 'user_permissions';
    }

    public function onActions(PageActionsEvent $event): void
    {
        if ($this->isGranted('role_permissions')) {
            $event->addCreate($this->path('admin_user_roles'), !$event->isView('role'));
        }
    }
}
