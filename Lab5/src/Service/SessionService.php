<?php

namespace App\Service;

use App\Entity\Session;
use App\Exception\BadRequestException;
use App\Repository\HallRepository;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class SessionService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestCheckerService $requestCheckerService,
        private MovieRepository $movieRepository,
        private HallRepository $hallRepository
    ) {}

    public function createSession(string $startTime, string $price, int $movieId, int $hallId): Session
    {
        $movie = $this->movieRepository->find($movieId);
        if (!$movie) {
            throw new BadRequestException("Movie with id $movieId not found", Response::HTTP_NOT_FOUND);
        }
        $hall = $this->hallRepository->find($hallId);
        if (!$hall) {
            throw new BadRequestException("Hall with id $hallId not found", Response::HTTP_NOT_FOUND);
        }

        $session = new Session();
        $session->setStartTime(new \DateTime($startTime));
        $session->setPrice($price);
        $session->setMovie($movie);
        $session->setHall($hall);

        $this->requestCheckerService->validateRequestDataByConstraints($session);

        $this->entityManager->persist($session);
        return $session;
    }

    public function updateSession(Session $session, array $data): void
    {
        if (isset($data['startTime'])) $session->setStartTime(new \DateTime($data['startTime']));
        if (isset($data['price'])) $session->setPrice($data['price']);

        if (isset($data['movie_id'])) {
            $movie = $this->movieRepository->find($data['movie_id']);
            if (!$movie) throw new BadRequestException("Movie with id {$data['movie_id']} not found", Response::HTTP_NOT_FOUND);
            $session->setMovie($movie);
        }
        if (isset($data['hall_id'])) {
            $hall = $this->hallRepository->find($data['hall_id']);
            if (!$hall) throw new BadRequestException("Hall with id {$data['hall_id']} not found", Response::HTTP_NOT_FOUND);
            $session->setHall($hall);
        }

        $this->requestCheckerService->validateRequestDataByConstraints($session);
    }
}