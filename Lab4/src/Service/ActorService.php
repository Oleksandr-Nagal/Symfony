<?php

namespace App\Service;

use App\Entity\Actor;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RequestCheckerService;

class ActorService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestCheckerService $requestCheckerService
    ) {}

    public function createActor(string $name): Actor
    {
        $actor = new Actor();
        $actor->setName($name);

        $this->requestCheckerService->validateRequestDataByConstraints($actor);

        $this->entityManager->persist($actor);
        return $actor;
    }

    public function updateActor(Actor $actor, array $data): void
    {
        if (isset($data['name'])) {
            $actor->setName($data['name']);
        }

        $this->requestCheckerService->validateRequestDataByConstraints($actor);
    }
}