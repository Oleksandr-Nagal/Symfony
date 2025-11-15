<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Service\MovieService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/movies')]
class MovieController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_MOVIE = ['title', 'description', 'duration', 'releaseDate'];

    public function __construct(
        private MovieRepository $repo,
        private MovieService $movieService,
        private RequestCheckerService $requestCheckerService,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'movie_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->repo->findAll(), 200, [], ['groups' => 'movie:read']);
    }

    #[Route('/{id}', name: 'movie_show', methods: ['GET'])]
    public function show(Movie $movie): JsonResponse
    {
        return $this->json($movie, 200, [], ['groups' => 'movie:read']);
    }

    #[Route('', name: 'movie_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_MOVIE);

        $movie = $this->movieService->createMovie(
            $requestData['title'],
            $requestData['description'],
            $requestData['duration'],
            new \DateTime($requestData['releaseDate']),
            $requestData['genres'] ?? [],
            $requestData['actors'] ?? [],
            $requestData['director_id'] ?? null
        );

        $this->entityManager->flush();

        return $this->json($movie, Response::HTTP_CREATED, [], ['groups' => 'movie:read']);
    }

    #[Route('/{id}', name: 'movie_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Movie $movie): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $this->movieService->updateMovie($movie, $requestData);
        $this->entityManager->flush();

        return $this->json($movie, Response::HTTP_OK, [], ['groups' => 'movie:read']);
    }

    #[Route('/{id}', name: 'movie_delete', methods: ['DELETE'])]
    public function delete(Movie $movie): JsonResponse
    {
        $this->entityManager->remove($movie);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
