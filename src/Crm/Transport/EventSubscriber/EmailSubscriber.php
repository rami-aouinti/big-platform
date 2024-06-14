<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber;

use App\Crm\Application\Mail\KimaiMailer;
use App\Crm\Transport\Event\EmailEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber to handle emails.
 */
final class EmailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly KimaiMailer $mailer
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EmailEvent::class => ['onMailEvent', 100],
        ];
    }

    public function onMailEvent(EmailEvent $event): void
    {
        $this->mailer->send($event->getEmail());
    }
}
