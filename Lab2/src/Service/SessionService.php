<?php

namespace App\Service;

use App\Entity\Session;
use App\Entity\Movie;
use App\Entity\Hall;
use Doctrine\ORM\EntityManagerInterface;

class SessionService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function createSession(\DateTime $startTime, float $price, Movie $movie, Hall $hall): Session
    {
        $session = new Session();
        $session->setStartTime($startTime);
        $session->setPrice($price);
        $session->setMovie($movie);
        $session->setHall($hall);
        $this->em->persist($session);
        $this->em->flush();
        return $session;
    }
}
