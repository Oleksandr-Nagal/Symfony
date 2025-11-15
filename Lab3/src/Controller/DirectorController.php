<?php

namespace App\Controller;

use App\Entity\Director;
use App\Repository\DirectorRepository;
use App\Service\DirectorService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

#[Route('/directors')]
class DirectorController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_DIRECTOR = ['name'];

    public function __construct(
        private DirectorRepository $repo,
        private DirectorService $directorService,
        private RequestCheckerService $requestCheckerService,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'director_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->repo->findAll(), 200, [], ['groups' => 'director:read']);
    }

    #[Route('/{id}', name: 'director_show', methods: ['GET'])]
    public function show(Director $director): JsonResponse
    {
        return $this->json($director, 200, [], ['groups' => 'director:read']);
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'director_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_DIRECTOR);

        $director = $this->directorService->createDirector($requestData['name']);

        $this->entityManager->flush();

        return $this->json($director, Response::HTTP_CREATED, [], ['groups' => 'director:read']);
    }

    #[Route('/{id}', name: 'director_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Director $director): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $this->directorService->updateDirector($director, $requestData);

        $this->entityManager->flush();

        return $this->json($director, Response::HTTP_OK, [], ['groups' => 'director:read']);
    }

    #[Route('/{id}', name: 'director_delete', methods: ['DELETE'])]
    public function delete(Director $director): JsonResponse
    {
        $this->entityManager->remove($director);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}