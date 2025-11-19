<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Service\ClientService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/clients')]
#[IsGranted('ROLE_USER')]
class ClientController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_CLIENT = ['email', 'name'];

    public function __construct(
        private readonly ClientRepository $repo,
        private readonly ClientService $clientService,
        private readonly RequestCheckerService $requestCheckerService,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route('', name: 'client_index', methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {
        return $this->json(
            $this->repo->findAll(),
            Response::HTTP_OK,
            [],
            ['groups' => 'client:read']
        );
    }

    #[Route('/{id}', name: 'client_show', methods: [Request::METHOD_GET])]
    public function show(Client $client): JsonResponse
    {
        return $this->json(
            $client,
            Response::HTTP_OK,
            [],
            ['groups' => 'client:read']
        );
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'client_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        try {
            $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_CLIENT);
        } catch (Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        $client = $this->clientService->createClient(
            $requestData['email'],
            $requestData['name'],
            $requestData['phone'] ?? null
        );

        $this->entityManager->flush();

        return $this->json(
            $client,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'client:read']
        );
    }

    #[Route('/{id}', name: 'client_update', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
    public function update(Request $request, Client $client): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        $this->clientService->updateClient($client, $requestData);
        $this->entityManager->flush();

        return $this->json(
            $client,
            Response::HTTP_OK,
            [],
            ['groups' => 'client:read']
        );
    }

    #[Route('/{id}', name: 'client_delete', methods: [Request::METHOD_DELETE])]
    public function delete(Client $client): JsonResponse
    {
        $this->entityManager->remove($client);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
