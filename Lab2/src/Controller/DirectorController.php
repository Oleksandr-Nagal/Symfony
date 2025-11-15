<?php

namespace App\Controller;

use App\Entity\Director;
use App\Repository\DirectorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/directors')]
class DirectorController extends AbstractController
{
    public function __construct(private DirectorRepository $repo) {}

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

    #[Route('', name: 'director_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $director = new Director();
        $director->setName($data['name']);
        $em->persist($director);
        $em->flush();

        return $this->json($director, 201, [], ['groups' => 'director:read']);
    }

    #[Route('/{id}', name: 'director_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Director $director, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) $director->setName($data['name']);
        $em->flush();

        return $this->json($director, 200, [], ['groups' => 'director:read']);
    }

    #[Route('/{id}', name: 'director_delete', methods: ['DELETE'])]
    public function delete(Director $director, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($director);
        $em->flush();

        return $this->json(null, 204);
    }
}