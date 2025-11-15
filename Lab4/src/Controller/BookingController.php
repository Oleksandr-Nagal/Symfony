<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Service\BookingService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bookings')]
class BookingController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_BOOKING = ['createdAt', 'client_id'];

    public function __construct(
        private BookingRepository $repo,
        private BookingService $bookingService,
        private RequestCheckerService $requestCheckerService,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'booking_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->repo->findAll(), 200, [], ['groups' => 'booking:read']);
    }

    #[Route('/{id}', name: 'booking_show', methods: ['GET'])]
    public function show(Booking $booking): JsonResponse
    {
        return $this->json($booking, 200, [], ['groups' => 'booking:read']);
    }

    #[Route('', name: 'booking_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_BOOKING);

        $booking = $this->bookingService->createBooking(
            new \DateTime($requestData['createdAt']),
            $requestData['client_id']
        );

        $this->entityManager->flush();

        return $this->json($booking, Response::HTTP_CREATED, [], ['groups' => 'booking:read']);
    }

    #[Route('/{id}', name: 'booking_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Booking $booking): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $this->bookingService->updateBooking($booking, $requestData);
        $this->entityManager->flush();

        return $this->json($booking, Response::HTTP_OK, [], ['groups' => 'booking:read']);
    }

    #[Route('/{id}', name: 'booking_delete', methods: ['DELETE'])]
    public function delete(Booking $booking): JsonResponse
    {
        $this->entityManager->remove($booking);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
