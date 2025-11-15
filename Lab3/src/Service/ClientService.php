<?php

namespace App\Service;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RequestCheckerService;

class ClientService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestCheckerService $requestCheckerService
    ) {}

    public function createClient(string $email, string $name, ?string $phone = null): Client
    {
        $client = new Client();
        $client->setEmail($email);
        $client->setName($name);
        $client->setPhone($phone);

        $this->requestCheckerService->validateRequestDataByConstraints($client);

        $this->entityManager->persist($client);
        return $client;
    }

    public function updateClient(Client $client, array $data): void
    {
        if (isset($data['email'])) $client->setEmail($data['email']);
        if (isset($data['name'])) $client->setName($data['name']);

        if (array_key_exists('phone', $data)) {
            $client->setPhone($data['phone']);
        }

        $this->requestCheckerService->validateRequestDataByConstraints($client);
    }
}