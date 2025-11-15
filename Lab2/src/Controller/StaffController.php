<?php

namespace App\Controller;

use App\Entity\Staff;
use App\Repository\StaffRepository;
use App\Entity\Hall;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/staffs')]
class StaffController extends AbstractController
{
    public function __construct(private StaffRepository $repo) {}

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

    #[Route('', name: 'staff_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $hall = $em->getRepository(Hall::class)->find($data['hall_id']);

        if (!$hall) {
            return $this->json(['error' => 'Hall not found'], 404);
        }

        $staff = new Staff();
        $staff->setName($data['name']);
        $staff->setPosition($data['position']);
        $staff->setHall($hall);
        $em->persist($staff);
        $em->flush();

        return $this->json($staff, 201, [], ['groups' => 'staff:read']);
    }

    #[Route('/{id}', name: 'staff_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Staff $staff, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) $staff->setName($data['name']);
        if (isset($data['position'])) $staff->setPosition($data['position']);

        if (isset($data['hall_id'])) {
            $hall = $em->getRepository(Hall::class)->find($data['hall_id']);
            if (!$hall) return $this->json(['error' => 'Hall not found'], 404);
            $staff->setHall($hall);
        }

        $em->flush();
        return $this->json($staff, 200, [], ['groups' => 'staff:read']);
    }

    #[Route('/{id}', name: 'staff_delete', methods: ['DELETE'])]
    public function delete(Staff $staff, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($staff);
        $em->flush();

        return $this->json(null, 204);
    }
}