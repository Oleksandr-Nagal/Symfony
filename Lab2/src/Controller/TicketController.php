<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use App\Entity\Session;
use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tickets')]
class TicketController extends AbstractController
{
    public function __construct(private TicketRepository $repo) {}

    #[Route('', name: 'ticket_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->repo->findAll(), 200, [], ['groups' => 'ticket:read']);
    }

    #[Route('/{id}', name: 'ticket_show', methods: ['GET'])]
    public function show(Ticket $ticket): JsonResponse
    {
        return $this->json($ticket, 200, [], ['groups' => 'ticket:read']);
    }

    #[Route('', name: 'ticket_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $session = $em->getRepository(Session::class)->find($data['session_id']);
        $booking = $em->getRepository(Booking::class)->find($data['booking_id']);

        if (!$session || !$booking) {
            return $this->json(['error' => 'Session or Booking not found'], 404);
        }

        $ticket = new Ticket();
        $ticket->setSeatNumber($data['seatNumber']);
        $ticket->setSession($session);
        $ticket->setBooking($booking);
        $em->persist($ticket);
        $em->flush();

        return $this->json($ticket, 201, [], ['groups' => 'ticket:read']);
    }

    #[Route('/{id}', name: 'ticket_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Ticket $ticket, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['seatNumber'])) $ticket->setSeatNumber($data['seatNumber']);

        if (isset($data['session_id'])) {
            $session = $em->getRepository(Session::class)->find($data['session_id']);
            if (!$session) return $this->json(['error' => 'Session not found'], 404);
            $ticket->setSession($session);
        }

        if (isset($data['booking_id'])) {
            $booking = $em->getRepository(Booking::class)->find($data['booking_id']);
            if (!$booking) return $this->json(['error' => 'Booking not found'], 404);
            $ticket->setBooking($booking);
        }

        $em->flush();
        return $this->json($ticket, 200, [], ['groups' => 'ticket:read']);
    }

    #[Route('/{id}', name: 'ticket_delete', methods: ['DELETE'])]
    public function delete(Ticket $ticket, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($ticket);
        $em->flush();

        return $this->json(null, 204);
    }
}