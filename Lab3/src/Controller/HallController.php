<?php

namespace App\Controller;

use App\Entity\Hall;
use App\Repository\HallRepository;
use App\Service\HallService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

#[Route('/halls')]
class HallController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_HALL = ['name', 'capacity'];

    public function __construct(
        private HallRepository $repo,
        private HallService $hallService,
        private RequestCheckerService $requestCheckerService,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'hall_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->repo->findAll(), 200, [], ['groups' => 'hall:read']);
    }

    #[Route('/{id}', name: 'hall_show', methods: ['GET'])]
    public function show(Hall $hall): JsonResponse
    {
        return $this->json($hall, 200, [], ['groups' => 'hall:read']);
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'hall_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_HALL);

        $hall = $this->hallService->createHall(
            $requestData['name'],
            $requestData['capacity']
        );

        $this->entityManager->flush();

        return $this->json($hall, Response::HTTP_CREATED, [], ['groups' => 'hall:read']);
    }

    #[Route('/{id}', name: 'hall_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Hall $hall): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $this->hallService->updateHall($hall, $requestData);

        $this->entityManager->flush();

        return $this->json($hall, Response::HTTP_OK, [], ['groups' => 'hall:read']);
    }

    #[Route('/{id}', name: 'hall_delete', methods: ['DELETE'])]
    public function delete(Hall $hall): JsonResponse
    {
        $this->entityManager->remove($hall);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}