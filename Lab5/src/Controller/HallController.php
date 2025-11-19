<?php

namespace App\Controller;

use App\Entity\Hall;
use App\Repository\HallRepository;
use App\Service\HallService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/halls')]
#[IsGranted('ROLE_USER')]
class HallController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_HALL = ['name', 'capacity'];

    public function __construct(
        private readonly HallRepository $repo,
        private readonly HallService $hallService,
        private readonly RequestCheckerService $requestCheckerService,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route('', name: 'hall_index', methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {
        return $this->json(
            $this->repo->findAll(),
            Response::HTTP_OK,
            [],
            ['groups' => 'hall:read']
        );
    }

    #[Route('/{id}', name: 'hall_show', methods: [Request::METHOD_GET])]
    public function show(Hall $hall): JsonResponse
    {
        return $this->json(
            $hall,
            Response::HTTP_OK,
            [],
            ['groups' => 'hall:read']
        );
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'hall_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        try {
            $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_HALL);
        } catch (Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        $hall = $this->hallService->createHall(
            $requestData['name'],
            $requestData['capacity']
        );

        $this->entityManager->flush();

        return $this->json(
            $hall,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'hall:read']
        );
    }

    #[Route('/{id}', name: 'hall_update', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
    public function update(Request $request, Hall $hall): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        $this->hallService->updateHall($hall, $requestData);
        $this->entityManager->flush();

        return $this->json(
            $hall,
            Response::HTTP_OK,
            [],
            ['groups' => 'hall:read']
        );
    }

    #[Route('/{id}', name: 'hall_delete', methods: [Request::METHOD_DELETE])]
    public function delete(Hall $hall): JsonResponse
    {
        $this->entityManager->remove($hall);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
