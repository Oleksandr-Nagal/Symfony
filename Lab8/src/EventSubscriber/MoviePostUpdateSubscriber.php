<?php

namespace App\EventSubscriber;

use App\Entity\Movie;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

class MoviePostUpdateSubscriber implements EventSubscriber
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
        if (!$entity instanceof Movie) {
            return;
        }

        $uow = $args->getObjectManager()->getUnitOfWork();
        $changes = $uow->getEntityChangeSet($entity);

        $this->logger->info('Movie updated', [
            'id' => $entity->getId(),
            'changes' => $changes,
        ]);
    }
}
