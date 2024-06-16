<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber\Actions;

use App\Crm\Transport\Event\PageActionsEvent;

final class TimesheetsTeamSubscriber extends AbstractActionsSubscriber
{
    public static function getActionName(): string
    {
        return 'timesheets_team';
    }

    public function onActions(PageActionsEvent $event): void
    {
        if ($this->isGranted('create_other_timesheet')) {
            $event->addAction('create', [
                'title' => 'create',
                'url' => $this->path('admin_timesheet_create'),
                'class' => 'create-ts modal-ajax-form',
            ]);
            $event->addAction('multi-user', [
                'title' => 'create-timesheet-multiuser',
                'translation_domain' => 'actions',
                'url' => $this->path('admin_timesheet_create_multiuser'),
                'class' => 'create-ts-mu modal-ajax-form',
                'icon' => 'fas fa-user-plus',
            ]);
        }

        if ($this->isGranted('export_other_timesheet')) {
            $event->addAction('download', [
                'url' => $this->path('admin_timesheet_export'),
                'class' => 'modal-ajax-form',
                'title' => 'export',
            ]);
        }
    }
}
