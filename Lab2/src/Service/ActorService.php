<?php

namespace App\Service;

use App\Entity\Actor;
use Doctrine\ORM\EntityManagerInterface;

class ActorService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function createActor(string $name): Actor
    {
        $actor = new Actor();
        $actor->setName($name);
        $this->em->persist($actor);
        $this->em->flush();
        return $actor;
    }
}
