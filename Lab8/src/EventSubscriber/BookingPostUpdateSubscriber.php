<?php

namespace App\EventSubscriber;

use App\Entity\Booking;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

class BookingPostUpdateSubscriber implements EventSubscriber
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    public function getSubscribedEvents(): array
    {
        return [Events::postUpdate];
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Booking) {
            return;
        }

        $uow = $args->getObjectManager()->getUnitOfWork();
        $changes = $uow->getEntityChangeSet($entity);

        $this->logger->info('Booking updated', [
            'id' => $entity->getId(),
            'changes' => $changes,
        ]);
    }
}
