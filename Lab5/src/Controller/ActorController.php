<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Repository\ActorRepository;
use App\Service\ActorService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/actors')]
#[IsGranted('ROLE_USER')]
class ActorController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_ACTOR = ['name'];

    public function __construct(
        private readonly ActorRepository $repo,
        private readonly ActorService $actorService,
        private readonly RequestCheckerService $requestCheckerService,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route('', name: 'actor_index', methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {
        return $this->json(
            $this->repo->findAll(),
            Response::HTTP_OK,
            [],
            ['groups' => 'actor:read']
        );
    }

    #[Route('/{id}', name: 'actor_show', methods: [Request::METHOD_GET])]
    public function show(Actor $actor): JsonResponse
    {
        return $this->json(
            $actor,
            Response::HTTP_OK,
            [],
            ['groups' => 'actor:read']
        );
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'actor_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        try {
            $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_ACTOR);
        } catch (Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        $actor = $this->actorService->createActor($requestData['name']);
        $this->entityManager->flush();

        return $this->json(
            $actor,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'actor:read']
        );
    }

    #[Route('/{id}', name: 'actor_update', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
    public function update(Request $request, Actor $actor): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        $this->actorService->updateActor($actor, $requestData);
        $this->entityManager->flush();

        return $this->json(
            $actor,
            Response::HTTP_OK,
            [],
            ['groups' => 'actor:read']
        );
    }

    #[Route('/{id}', name: 'actor_delete', methods: [Request::METHOD_DELETE])]
    public function delete(Actor $actor): JsonResponse
    {
        $this->entityManager->remove($actor);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
