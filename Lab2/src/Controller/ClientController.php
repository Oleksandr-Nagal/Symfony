<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/clients')]
class ClientController extends AbstractController
{
    public function __construct(private ClientRepository $repo) {}

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
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $client = new Client();
        $client->setEmail($data['email']);
        $client->setName($data['name']);
        $client->setPhone($data['phone'] ?? null);
        $em->persist($client);
        $em->flush();

        return $this->json($client, 201, [], ['groups' => 'client:read']);
    }

    #[Route('/{id}', name: 'client_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Client $client, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['email'])) $client->setEmail($data['email']);
        if (isset($data['name'])) $client->setName($data['name']);
        if (isset($data['phone'])) $client->setPhone($data['phone']);
        $em->flush();

        return $this->json($client, 200, [], ['groups' => 'client:read']);
    }

    #[Route('/{id}', name: 'client_delete', methods: ['DELETE'])]
    public function delete(Client $client, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($client);
        $em->flush();

        return $this->json(null, 204);
    }
}