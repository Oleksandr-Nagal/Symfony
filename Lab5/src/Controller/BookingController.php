<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Service\BookingService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/bookings')]
#[IsGranted('ROLE_USER')]
class BookingController extends AbstractController
{
    private const REQUIRED_FIELDS_FOR_CREATE_BOOKING = ['createdAt', 'client_id'];

    public function __construct(
        private readonly BookingRepository $repo,
        private readonly BookingService $bookingService,
        private readonly RequestCheckerService $requestCheckerService,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route('', name: 'booking_index', methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {
        return $this->json(
            $this->repo->findAll(),
            Response::HTTP_OK,
            [],
            ['groups' => 'booking:read']
        );
    }

    #[Route('/{id}', name: 'booking_show', methods: [Request::METHOD_GET])]
    public function show(Booking $booking): JsonResponse
    {
        return $this->json(
            $booking,
            Response::HTTP_OK,
            [],
            ['groups' => 'booking:read']
        );
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'booking_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        try {
            $this->requestCheckerService->check($requestData, self::REQUIRED_FIELDS_FOR_CREATE_BOOKING);
        } catch (Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        $booking = $this->bookingService->createBooking(
            new \DateTime($requestData['createdAt']),
            $requestData['client_id']
        );

        $this->entityManager->flush();

        return $this->json(
            $booking,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'booking:read']
        );
    }

    #[Route('/{id}', name: 'booking_update', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
    public function update(Request $request, Booking $booking): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];

        $this->bookingService->updateBooking($booking, $requestData);
        $this->entityManager->flush();

        return $this->json(
            $booking,
            Response::HTTP_OK,
            [],
            ['groups' => 'booking:read']
        );
    }

    #[Route('/{id}', name: 'booking_delete', methods: [Request::METHOD_DELETE])]
    public function delete(Booking $booking): JsonResponse
    {
        $this->entityManager->remove($booking);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
