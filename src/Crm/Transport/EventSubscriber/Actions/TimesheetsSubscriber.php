<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber\Actions;

use App\Crm\Transport\Event\PageActionsEvent;

final class TimesheetsSubscriber extends AbstractActionsSubscriber
{
    public static function getActionName(): string
    {
        return 'timesheets';
    }

    public function onActions(PageActionsEvent $event): void
    {
        if ($this->isGranted('create_own_timesheet')) {
            $event->addCreate($this->path('timesheet_create'));
        }

        if ($this->isGranted('export_own_timesheet')) {
            $event->addAction('download', [
                'url' => $this->path('timesheet_export'),
                'class' => 'modal-ajax-form',
                'title' => 'export',
            ]);
        }
    }
}
