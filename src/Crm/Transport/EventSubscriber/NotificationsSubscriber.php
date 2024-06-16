<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber;

use KevinPapst\TablerBundle\Event\NotificationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationsSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            NotificationEvent::class => ['onNotificationEvent', 100],
        ];
    }

    public function onNotificationEvent(NotificationEvent $event): void
    {
        $event->setShowBadgeTotal(false);
    }
}
