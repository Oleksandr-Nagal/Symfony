<?php

namespace App\Service;

use App\Entity\Ticket;
use App\Exception\BadRequestException;
use App\Repository\BookingRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class TicketService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestCheckerService $requestCheckerService,
        private SessionRepository $sessionRepository,
        private BookingRepository $bookingRepository
    ) {}

    public function createTicket(string $seatNumber, int $sessionId, int $bookingId): Ticket
    {
        $session = $this->sessionRepository->find($sessionId);
        if (!$session) {
            throw new BadRequestException("Session with id $sessionId not found", Response::HTTP_NOT_FOUND);
        }
        $booking = $this->bookingRepository->find($bookingId);
        if (!$booking) {
            throw new BadRequestException("Booking with id $bookingId not found", Response::HTTP_NOT_FOUND);
        }

        $ticket = new Ticket();
        $ticket->setSeatNumber($seatNumber);
        $ticket->setSession($session);
        $ticket->setBooking($booking);

        $this->requestCheckerService->validateRequestDataByConstraints($ticket);

        $this->entityManager->persist($ticket);
        return $ticket;
    }

    public function updateTicket(Ticket $ticket, array $data): void
    {
        if (isset($data['seatNumber'])) $ticket->setSeatNumber($data['seatNumber']);

        if (isset($data['session_id'])) {
            $session = $this->sessionRepository->find($data['session_id']);
            if (!$session) throw new BadRequestException("Session with id {$data['session_id']} not found", Response::HTTP_NOT_FOUND);
            $ticket->setSession($session);
        }
        if (isset($data['booking_id'])) {
            $booking = $this->bookingRepository->find($data['booking_id']);
            if (!$booking) throw new BadRequestException("Booking with id {$data['booking_id']} not found", Response::HTTP_NOT_FOUND);
            $ticket->setBooking($booking);
        }

        $this->requestCheckerService->validateRequestDataByConstraints($ticket);
    }
}