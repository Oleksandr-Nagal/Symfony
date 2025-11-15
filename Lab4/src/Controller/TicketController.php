<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use App\Service\TicketService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tickets')]
class TicketController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_TICKET = ['seatNumber', 'session_id', 'booking_id'];

    public function __construct(
        private TicketRepository $repo,
        private TicketService $ticketService,
        private RequestCheckerService $requestCheckerService,
        private EntityManagerInterface $entityManager
    ) {}

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
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_TICKET);

        $ticket = $this->ticketService->createTicket(
            $requestData['seatNumber'],
            $requestData['session_id'],
            $requestData['booking_id']
        );

        $this->entityManager->flush();

        return $this->json($ticket, Response::HTTP_CREATED, [], ['groups' => 'ticket:read']);
    }

    #[Route('/{id}', name: 'ticket_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Ticket $ticket): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $this->ticketService->updateTicket($ticket, $requestData);
        $this->entityManager->flush();

        return $this->json($ticket, Response::HTTP_OK, [], ['groups' => 'ticket:read']);
    }

    #[Route('/{id}', name: 'ticket_delete', methods: ['DELETE'])]
    public function delete(Ticket $ticket): JsonResponse
    {
        $this->entityManager->remove($ticket);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
