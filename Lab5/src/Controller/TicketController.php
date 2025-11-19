<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use App\Service\RequestCheckerService;
use App\Service\TicketService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tickets')]
#[IsGranted('ROLE_USER')]
class TicketController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_TICKET = [
        'seatNumber',
        'session_id',
        'booking_id',
    ];

    public function __construct(
        private readonly TicketRepository $repo,
        private readonly TicketService $ticketService,
        private readonly RequestCheckerService $requestCheckerService,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route('', name: 'ticket_index', methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {
        return $this->json(
            $this->repo->findAll(),
            Response::HTTP_OK,
            [],
            ['groups' => 'ticket:read']
        );
    }

    #[Route('/{id}', name: 'ticket_show', methods: [Request::METHOD_GET])]
    public function show(Ticket $ticket): JsonResponse
    {
        return $this->json(
            $ticket,
            Response::HTTP_OK,
            [],
            ['groups' => 'ticket:read']
        );
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'ticket_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        try {
            $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_TICKET);
        } catch (Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        $ticket = $this->ticketService->createTicket(
            $requestData['seatNumber'],
            $requestData['session_id'],
            $requestData['booking_id']
        );

        $this->entityManager->flush();

        return $this->json(
            $ticket,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'ticket:read']
        );
    }

    #[Route('/{id}', name: 'ticket_update', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
    public function update(Request $request, Ticket $ticket): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        $this->ticketService->updateTicket($ticket, $requestData);
        $this->entityManager->flush();

        return $this->json(
            $ticket,
            Response::HTTP_OK,
            [],
            ['groups' => 'ticket:read']
        );
    }

    #[Route('/{id}', name: 'ticket_delete', methods: [Request::METHOD_DELETE])]
    public function delete(Ticket $ticket): JsonResponse
    {
        $this->entityManager->remove($ticket);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
