<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\SessionRepository;
use App\Service\RequestCheckerService;
use App\Service\SessionService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sessions')]
#[IsGranted('ROLE_USER')]
class SessionController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_SESSION = [
        'startTime',
        'price',
        'movie_id',
        'hall_id',
    ];

    public function __construct(
        private readonly SessionRepository $repo,
        private readonly SessionService $sessionService,
        private readonly RequestCheckerService $requestCheckerService,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route('', name: 'session_index', methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {
        return $this->json(
            $this->repo->findAll(),
            Response::HTTP_OK,
            [],
            ['groups' => 'session:read']
        );
    }

    #[Route('/{id}', name: 'session_show', methods: [Request::METHOD_GET])]
    public function show(Session $session): JsonResponse
    {
        return $this->json(
            $session,
            Response::HTTP_OK,
            [],
            ['groups' => 'session:read']
        );
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'session_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        try {
            $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_SESSION);
        } catch (Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        $session = $this->sessionService->createSession(
            new \DateTime($requestData['startTime']),
            $requestData['price'],
            $requestData['movie_id'],
            $requestData['hall_id']
        );

        $this->entityManager->flush();

        return $this->json(
            $session,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'session:read']
        );
    }

    #[Route('/{id}', name: 'session_update', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
    public function update(Request $request, Session $session): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        $this->sessionService->updateSession($session, $requestData);
        $this->entityManager->flush();

        return $this->json(
            $session,
            Response::HTTP_OK,
            [],
            ['groups' => 'session:read']
        );
    }

    #[Route('/{id}', name: 'session_delete', methods: [Request::METHOD_DELETE])]
    public function delete(Session $session): JsonResponse
    {
        $this->entityManager->remove($session);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
