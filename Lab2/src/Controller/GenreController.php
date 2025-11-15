<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/genres')]
class GenreController extends AbstractController
{
    public function __construct(private GenreRepository $repo) {}

    #[Route('', name: 'genre_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->repo->findAll(), 200, [], ['groups' => 'genre:read']);
    }

    #[Route('/{id}', name: 'genre_show', methods: ['GET'])]
    public function show(Genre $genre): JsonResponse
    {
        return $this->json($genre, 200, [], ['groups' => 'genre:read']);
    }

    #[Route('', name: 'genre_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $genre = new Genre();
        $genre->setName($data['name']);
        $em->persist($genre);
        $em->flush();

        return $this->json($genre, 201, [], ['groups' => 'genre:read']);
    }

    #[Route('/{id}', name: 'genre_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Genre $genre, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) $genre->setName($data['name']);
        $em->flush();

        return $this->json($genre, 200, [], ['groups' => 'genre:read']);
    }

    #[Route('/{id}', name: 'genre_delete', methods: ['DELETE'])]
    public function delete(Genre $genre, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($genre);
        $em->flush();

        return $this->json(null, 204);
    }
}