<?php

namespace App\Controller;

use App\Entity\Director;
use App\Repository\DirectorRepository;
use App\Service\DirectorService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/directors')]
#[IsGranted('ROLE_USER')]
class DirectorController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_DIRECTOR = ['name'];

    public function __construct(
        private readonly DirectorRepository $repo,
        private readonly DirectorService $directorService,
        private readonly RequestCheckerService $requestCheckerService,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route('', name: 'director_index', methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {
        return $this->json(
            $this->repo->findAll(),
            Response::HTTP_OK,
            [],
            ['groups' => 'director:read']
        );
    }

    #[Route('/{id}', name: 'director_show', methods: [Request::METHOD_GET])]
    public function show(Director $director): JsonResponse
    {
        return $this->json(
            $director,
            Response::HTTP_OK,
            [],
            ['groups' => 'director:read']
        );
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'director_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        try {
            $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_DIRECTOR);
        } catch (Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        $director = $this->directorService->createDirector($requestData['name']);
        $this->entityManager->flush();

        return $this->json(
            $director,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'director:read']
        );
    }

    #[Route('/{id}', name: 'director_update', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
    public function update(Request $request, Director $director): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        $this->directorService->updateDirector($director, $requestData);
        $this->entityManager->flush();

        return $this->json(
            $director,
            Response::HTTP_OK,
            [],
            ['groups' => 'director:read']
        );
    }

    #[Route('/{id}', name: 'director_delete', methods: [Request::METHOD_DELETE])]
    public function delete(Director $director): JsonResponse
    {
        $this->entityManager->remove($director);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
