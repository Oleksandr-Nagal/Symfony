<?php

namespace App\Controller;

use App\Entity\Hall;
use App\Repository\HallRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/halls')]
class HallController extends AbstractController
{
    public function __construct(private HallRepository $repo) {}

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

    #[Route('', name: 'hall_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $hall = new Hall();
        $hall->setName($data['name']);
        $hall->setCapacity($data['capacity']);
        $em->persist($hall);
        $em->flush();

        return $this->json($hall, 201, [], ['groups' => 'hall:read']);
    }

    #[Route('/{id}', name: 'hall_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Hall $hall, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) $hall->setName($data['name']);
        if (isset($data['capacity'])) $hall->setCapacity($data['capacity']);
        $em->flush();

        return $this->json($hall, 200, [], ['groups' => 'hall:read']);
    }

    #[Route('/{id}', name: 'hall_delete', methods: ['DELETE'])]
    public function delete(Hall $hall, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($hall);
        $em->flush();

        return $this->json(null, 204);
    }
}