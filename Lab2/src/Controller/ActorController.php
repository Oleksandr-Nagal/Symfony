<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Repository\ActorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/actors')]
class ActorController extends AbstractController
{
    public function __construct(private ActorRepository $repo) {}

    #[Route('', name: 'actor_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        // ВКАЖИ ГРУПУ ТУТ
        return $this->json($this->repo->findAll(), 200, [], ['groups' => 'actor:read']);
    }

    #[Route('/{id}', name: 'actor_show', methods: ['GET'])]
    public function show(Actor $actor): JsonResponse
    {
        // І ВКАЖИ ГРУПУ ТУТ
        return $this->json($actor, 200, [], ['groups' => 'actor:read']);
    }

    #[Route('', name: 'actor_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $actor = new Actor();
        $actor->setName($data['name']);
        $em->persist($actor);
        $em->flush();
        return $this->json($actor);
    }

    #[Route('/{id}', name: 'actor_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Actor $actor, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) $actor->setName($data['name']);
        $em->flush();
        return $this->json($actor);
    }

    #[Route('/{id}', name: 'actor_delete', methods: ['DELETE'])]
    public function delete(Actor $actor, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($actor);
        $em->flush();
        return $this->json(['deleted' => true]);
    }
}
