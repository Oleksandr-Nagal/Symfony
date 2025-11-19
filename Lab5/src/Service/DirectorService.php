<?php

namespace App\Service;

use App\Entity\Director;
use Doctrine\ORM\EntityManagerInterface;


class DirectorService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestCheckerService $requestCheckerService
    ) {}


    public function createDirector(string $name): Director
    {
        $director = $this->createDirectorObject($name);

        $this->requestCheckerService->validateRequestDataByConstraints($director);

        $this->entityManager->persist($director);
        return $director;
    }

    public function updateDirector(Director $director, array $data): void
    {
        if (isset($data['name'])) {
            $director->setName($data['name']);
        }

        $this->requestCheckerService->validateRequestDataByConstraints($director);
    }


    private function createDirectorObject(string $name): Director
    {
        $director = new Director();
        $director->setName($name);
        return $director;
    }
}