<?php

namespace App\Controller;

use App\Entity\Staff;
use App\Repository\StaffRepository;
use App\Service\RequestCheckerService;
use App\Service\StaffService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/staffs')]
#[IsGranted('ROLE_USER')]
class StaffController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_STAFF = [
        'name',
        'position',
        'hall_id',
    ];

    public function __construct(
        private readonly StaffRepository $repo,
        private readonly StaffService $staffService,
        private readonly RequestCheckerService $requestCheckerService,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route('', name: 'staff_index', methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {
        return $this->json(
            $this->repo->findAll(),
            Response::HTTP_OK,
            [],
            ['groups' => 'staff:read']
        );
    }

    #[Route('/{id}', name: 'staff_show', methods: [Request::METHOD_GET])]
    public function show(Staff $staff): JsonResponse
    {
        return $this->json(
            $staff,
            Response::HTTP_OK,
            [],
            ['groups' => 'staff:read']
        );
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'staff_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        try {
            $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_STAFF);
        } catch (Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        $staff = $this->staffService->createStaff(
            $requestData['name'],
            $requestData['position'],
            $requestData['hall_id']
        );

        $this->entityManager->flush();

        return $this->json(
            $staff,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'staff:read']
        );
    }

    #[Route('/{id}', name: 'staff_update', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
    public function update(Request $request, Staff $staff): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        $this->staffService->updateStaff($staff, $requestData);
        $this->entityManager->flush();

        return $this->json(
            $staff,
            Response::HTTP_OK,
            [],
            ['groups' => 'staff:read']
        );
    }

    #[Route('/{id}', name: 'staff_delete', methods: [Request::METHOD_DELETE])]
    public function delete(Staff $staff): JsonResponse
    {
        $this->entityManager->remove($staff);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
