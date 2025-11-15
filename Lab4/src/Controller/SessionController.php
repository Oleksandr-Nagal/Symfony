<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\SessionRepository;
use App\Service\SessionService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sessions')]
class SessionController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_SESSION = ['startTime', 'price', 'movie_id', 'hall_id'];

    public function __construct(
        private SessionRepository $repo,
        private SessionService $sessionService,
        private RequestCheckerService $requestCheckerService,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'session_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->repo->findAll(), 200, [], ['groups' => 'session:read']);
    }

    #[Route('/{id}', name: 'session_show', methods: ['GET'])]
    public function show(Session $session): JsonResponse
    {
        return $this->json($session, 200, [], ['groups' => 'session:read']);
    }

    #[Route('', name: 'session_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_SESSION);

        $session = $this->sessionService->createSession(
            new \DateTime($requestData['startTime']),
            $requestData['price'],
            $requestData['movie_id'],
            $requestData['hall_id']
        );

        $this->entityManager->flush();

        return $this->json($session, Response::HTTP_CREATED, [], ['groups' => 'session:read']);
    }

    #[Route('/{id}', name: 'session_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Session $session): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $this->sessionService->updateSession($session, $requestData);
        $this->entityManager->flush();

        return $this->json($session, Response::HTTP_OK, [], ['groups' => 'session:read']);
    }

    #[Route('/{id}', name: 'session_delete', methods: ['DELETE'])]
    public function delete(Session $session): JsonResponse
    {
        $this->entityManager->remove($session);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
