<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use App\Service\GenreService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/genres')]
#[IsGranted('ROLE_USER')]
class GenreController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_GENRE = ['name'];

    public function __construct(
        private readonly GenreRepository $repo,
        private readonly GenreService $genreService,
        private readonly RequestCheckerService $requestCheckerService,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route('', name: 'genre_index', methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {
        return $this->json(
            $this->repo->findAll(),
            Response::HTTP_OK,
            [],
            ['groups' => 'genre:read']
        );
    }

    #[Route('/{id}', name: 'genre_show', methods: [Request::METHOD_GET])]
    public function show(Genre $genre): JsonResponse
    {
        return $this->json(
            $genre,
            Response::HTTP_OK,
            [],
            ['groups' => 'genre:read']
        );
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'genre_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        try {
            $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_GENRE);
        } catch (Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        $genre = $this->genreService->createGenre($requestData['name']);
        $this->entityManager->flush();

        return $this->json(
            $genre,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'genre:read']
        );
    }

    #[Route('/{id}', name: 'genre_update', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
    public function update(Request $request, Genre $genre): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        $this->genreService->updateGenre($genre, $requestData);
        $this->entityManager->flush();

        return $this->json(
            $genre,
            Response::HTTP_OK,
            [],
            ['groups' => 'genre:read']
        );
    }

    #[Route('/{id}', name: 'genre_delete', methods: [Request::METHOD_DELETE])]
    public function delete(Genre $genre): JsonResponse
    {
        $this->entityManager->remove($genre);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
