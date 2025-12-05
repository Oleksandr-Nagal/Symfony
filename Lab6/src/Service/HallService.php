<?php

namespace App\Service;

use App\Entity\Hall;
use Doctrine\ORM\EntityManagerInterface;

class HallService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestCheckerService $requestCheckerService
    ) {}

    public function createHall(string $name, int $capacity): Hall
    {
        $hall = new Hall();
        $hall->setName($name);
        $hall->setCapacity($capacity);

        $this->requestCheckerService->validateRequestDataByConstraints($hall);

        $this->entityManager->persist($hall);
        return $hall;
    }

    public function updateHall(Hall $hall, array $data): void
    {
        if (isset($data['name'])) $hall->setName($data['name']);
        if (isset($data['capacity'])) $hall->setCapacity($data['capacity']);

        $this->requestCheckerService->validateRequestDataByConstraints($hall);
    }
}