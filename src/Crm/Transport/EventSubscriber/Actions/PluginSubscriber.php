<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber\Actions;

use App\Crm\Transport\Event\PageActionsEvent;
use App\Plugin\Plugin;

final class PluginSubscriber extends AbstractActionsSubscriber
{
    public static function getActionName(): string
    {
        return 'plugin';
    }

    public function onActions(PageActionsEvent $event): void
    {
        $payload = $event->getPayload();

        /** @var Plugin $plugin */
        $plugin = $payload['plugin'];

        $event->addAction('home', [
            'url' => $plugin->getMetadata()->getHomepage(),
            'target' => '_blank',
        ]);
    }
}
