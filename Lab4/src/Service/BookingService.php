<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Client;
use App\Exception\BadRequestException;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RequestCheckerService;
use Symfony\Component\HttpFoundation\Response;

class BookingService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestCheckerService $requestCheckerService,
        private ClientRepository $clientRepository
    ) {}

    public function createBooking(string $createdAt, int $clientId): Booking
    {
        $client = $this->clientRepository->find($clientId);
        if (!$client) {
            throw new BadRequestException("Client with id $clientId not found", Response::HTTP_NOT_FOUND);
        }

        $booking = new Booking();
        $booking->setCreatedAt(new \DateTime($createdAt));
        $booking->setClient($client);

        $this->requestCheckerService->validateRequestDataByConstraints($booking);

        $this->entityManager->persist($booking);
        return $booking;
    }

    public function updateBooking(Booking $booking, array $data): void
    {
        if (isset($data['createdAt'])) {
            $booking->setCreatedAt(new \DateTime($data['createdAt']));
        }
        if (isset($data['client_id'])) {
            $client = $this->clientRepository->find($data['client_id']);
            if (!$client) {
                throw new BadRequestException("Client with id {$data['client_id']} not found", Response::HTTP_NOT_FOUND);
            }
            $booking->setClient($client);
        }

        $this->requestCheckerService->validateRequestDataByConstraints($booking);
    }
}