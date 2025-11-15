<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Service\ClientService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/clients')]
class ClientController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_CLIENT = ['email', 'name'];

    public function __construct(
        private ClientRepository $repo,
        private ClientService $clientService,
        private RequestCheckerService $requestCheckerService,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'client_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->repo->findAll(), 200, [], ['groups' => 'client:read']);
    }

    #[Route('/{id}', name: 'client_show', methods: ['GET'])]
    public function show(Client $client): JsonResponse
    {
        return $this->json($client, 200, [], ['groups' => 'client:read']);
    }

    #[Route('', name: 'client_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_CLIENT);

        $client = $this->clientService->createClient(
            $requestData['email'],
            $requestData['name'],
            $requestData['phone'] ?? null
        );

        $this->entityManager->flush();

        return $this->json($client, Response::HTTP_CREATED, [], ['groups' => 'client:read']);
    }

    #[Route('/{id}', name: 'client_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Client $client): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $this->clientService->updateClient($client, $requestData);
        $this->entityManager->flush();

        return $this->json($client, Response::HTTP_OK, [], ['groups' => 'client:read']);
    }

    #[Route('/{id}', name: 'client_delete', methods: ['DELETE'])]
    public function delete(Client $client): JsonResponse
    {
        $this->entityManager->remove($client);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
