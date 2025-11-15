<?php

namespace App\Service;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

class ClientService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function createClient(string $email, string $name, ?string $phone = null): Client
    {
        $client = new Client();
        $client->setEmail($email);
        $client->setName($name);
        $client->setPhone($phone);
        $this->em->persist($client);
        $this->em->flush();
        return $client;
    }
}
