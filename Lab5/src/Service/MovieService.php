<?php

namespace App\Service;

use App\Entity\Director;
use App\Entity\Movie;
use App\Repository\DirectorRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;


class MovieService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestCheckerService $requestCheckerService,
        private DirectorRepository $directorRepository
    ) {}

    public function createMovie(
        string $title,
        string $description,
        int $duration,
        string $releaseDate,
        int $directorId
    ): Movie {
        $director = $this->directorRepository->find($directorId);
        if (!$director) {
            throw new BadRequestException("Director with id $directorId not found", Response::HTTP_NOT_FOUND);
        }

        $movie = $this->createMovieObject($title, $description, $duration, $releaseDate, $director);

        $this->requestCheckerService->validateRequestDataByConstraints($movie);

        $this->entityManager->persist($movie);
        return $movie;
    }

    public function updateMovie(Movie $movie, array $data): void
    {
        foreach ($data as $key => $value) {
            if ($key === 'director_id') {
                $director = $this->directorRepository->find($value);
                if (!$director) {
                    throw new BadRequestException("Director with id $value not found", Response::HTTP_NOT_FOUND);
                }
                $movie->setDirector($director);
                continue;
            }

            $method = 'set' . ucfirst(strtolower($key));
            if (!method_exists($movie, $method)) {
                continue;
            }

            // Окремо обробляємо дату
            if ($key === 'releaseDate') {
                $movie->setReleaseDate(new \DateTime($value));
                continue;
            }

            $movie->$method($value);
        }

        $this->requestCheckerService->validateRequestDataByConstraints($movie);
    }

    private function createMovieObject(
        string $title,
        string $description,
        int $duration,
        string $releaseDate,
        Director $director
    ): Movie {
        $movie = new Movie();
        $movie
            ->setTitle($title)
            ->setDescription($description)
            ->setDuration($duration)
            ->setReleaseDate(new \DateTime($releaseDate))
            ->setDirector($director);
        return $movie;
    }
}