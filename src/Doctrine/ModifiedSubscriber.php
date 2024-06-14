<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

/**
 * Automatically set the modifiedAt field for all ModifiedAt instances.
 */
#[AsDoctrineListener(event: Events::onFlush, priority: 60)]
final class ModifiedSubscriber implements EventSubscriber, DataSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $uow = $args->getObjectManager()->getUnitOfWork();
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof ModifiedAt) {
                $entity->setModifiedAt($now);
            }
        }

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof ModifiedAt) {
                $entity->setModifiedAt($now);
            }
        }
    }
}
