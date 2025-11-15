<?php

namespace App\Controller;

use App\Entity\Staff;
use App\Repository\StaffRepository;
use App\Service\StaffService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

#[Route('/staffs')]
class StaffController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_STAFF = ['name', 'position', 'hall_id'];

    public function __construct(
        private StaffRepository $repo,
        private StaffService $staffService,
        private RequestCheckerService $requestCheckerService,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'staff_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->repo->findAll(), 200, [], ['groups' => 'staff:read']);
    }

    #[Route('/{id}', name: 'staff_show', methods: ['GET'])]
    public function show(Staff $staff): JsonResponse
    {
        return $this->json($staff, 200, [], ['groups' => 'staff:read']);
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'staff_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_STAFF);

        $staff = $this->staffService->createStaff(
            $requestData['name'],
            $requestData['position'],
            $requestData['hall_id']
        );

        $this->entityManager->flush();

        return $this->json($staff, Response::HTTP_CREATED, [], ['groups' => 'staff:read']);
    }

    #[Route('/{id}', name: 'staff_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Staff $staff): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $this->staffService->updateStaff($staff, $requestData);

        $this->entityManager->flush();

        return $this->json($staff, Response::HTTP_OK, [], ['groups' => 'staff:read']);
    }

    #[Route('/{id}', name: 'staff_delete', methods: ['DELETE'])]
    public function delete(Staff $staff): JsonResponse
    {
        $this->entityManager->remove($staff);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}