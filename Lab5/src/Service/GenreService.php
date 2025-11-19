<?php

namespace App\Service;

use App\Entity\Genre;
use Doctrine\ORM\EntityManagerInterface;

class GenreService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestCheckerService $requestCheckerService
    ) {}

    public function createGenre(string $name): Genre
    {
        $genre = new Genre();
        $genre->setName($name);

        $this->requestCheckerService->validateRequestDataByConstraints($genre);

        $this->entityManager->persist($genre);
        return $genre;
    }

    public function updateGenre(Genre $genre, array $data): void
    {
        if (isset($data['name'])) {
            $genre->setName($data['name']);
        }

        $this->requestCheckerService->validateRequestDataByConstraints($genre);
    }
}