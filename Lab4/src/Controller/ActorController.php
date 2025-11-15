<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Repository\ActorRepository;
use App\Service\ActorService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

#[Route('/actors')]
class ActorController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_ACTOR = ['name'];

    public function __construct(
        private ActorRepository $repo,
        private ActorService $actorService,
        private RequestCheckerService $requestCheckerService,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'actor_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->repo->findAll(), 200, [], ['groups' => 'actor:read']);
    }

    #[Route('/{id}', name: 'actor_show', methods: ['GET'])]
    public function show(Actor $actor): JsonResponse
    {
        return $this->json($actor, 200, [], ['groups' => 'actor:read']);
    }

    #[Route('', name: 'actor_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_ACTOR);

        $actor = $this->actorService->createActor($requestData['name']);
        $this->entityManager->flush();

        return $this->json($actor, Response::HTTP_CREATED, [], ['groups' => 'actor:read']);
    }

    #[Route('/{id}', name: 'actor_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Actor $actor): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $this->actorService->updateActor($actor, $requestData);
        $this->entityManager->flush();

        return $this->json($actor, Response::HTTP_OK, [], ['groups' => 'actor:read']);
    }

    #[Route('/{id}', name: 'actor_delete', methods: ['DELETE'])]
    public function delete(Actor $actor): JsonResponse
    {
        $this->entityManager->remove($actor);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
