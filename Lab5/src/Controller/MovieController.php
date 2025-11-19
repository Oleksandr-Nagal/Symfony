<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Service\MovieService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/movies')]
#[IsGranted('ROLE_USER')]
class MovieController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_MOVIE = [
        'title',
        'description',
        'duration',
        'releaseDate',
    ];

    public function __construct(
        private readonly MovieRepository $repo,
        private readonly MovieService $movieService,
        private readonly RequestCheckerService $requestCheckerService,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route('', name: 'movie_index', methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {
        return $this->json(
            $this->repo->findAll(),
            Response::HTTP_OK,
            [],
            ['groups' => 'movie:read']
        );
    }

    #[Route('/{id}', name: 'movie_show', methods: [Request::METHOD_GET])]
    public function show(Movie $movie): JsonResponse
    {
        return $this->json(
            $movie,
            Response::HTTP_OK,
            [],
            ['groups' => 'movie:read']
        );
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'movie_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        try {
            $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_MOVIE);
        } catch (Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

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

        return $this->json(
            $movie,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'movie:read']
        );
    }

    #[Route('/{id}', name: 'movie_update', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
    public function update(Request $request, Movie $movie): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        $this->movieService->updateMovie($movie, $requestData);
        $this->entityManager->flush();

        return $this->json(
            $movie,
            Response::HTTP_OK,
            [],
            ['groups' => 'movie:read']
        );
    }

    #[Route('/{id}', name: 'movie_delete', methods: [Request::METHOD_DELETE])]
    public function delete(Movie $movie): JsonResponse
    {
        $this->entityManager->remove($movie);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
