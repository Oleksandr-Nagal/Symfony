<?php

namespace App\Service;

use App\Entity\Hall;
use Doctrine\ORM\EntityManagerInterface;

class HallService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function createHall(string $name, int $capacity): Hall
    {
        $hall = new Hall();
        $hall->setName($name);
        $hall->setCapacity($capacity);
        $this->em->persist($hall);
        $this->em->flush();
        return $hall;
    }
}
