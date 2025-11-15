<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bookings')]
class BookingController extends AbstractController
{
    public function __construct(private BookingRepository $repo) {}

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
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $client = $em->getRepository(Client::class)->find($data['client_id']);

        if (!$client) {
            return $this->json(['error' => 'Client not found'], 404);
        }

        $booking = new Booking();
        $booking->setCreatedAt(new \DateTime($data['createdAt']));
        $booking->setClient($client);
        $em->persist($booking);
        $em->flush();

        return $this->json($booking, 201, [], ['groups' => 'booking:read']);
    }

    #[Route('/{id}', name: 'booking_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Booking $booking, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['createdAt'])) $booking->setCreatedAt(new \DateTime($data['createdAt']));

        if (isset($data['client_id'])) {
            $client = $em->getRepository(Client::class)->find($data['client_id']);
            if (!$client) {
                return $this->json(['error' => 'Client not found'], 404);
            }
            $booking->setClient($client);
        }

        $em->flush();
        return $this->json($booking, 200, [], ['groups' => 'booking:read']);
    }

    #[Route('/{id}', name: 'booking_delete', methods: ['DELETE'])]
    public function delete(Booking $booking, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($booking);
        $em->flush();
        return $this->json(null, 204);
    }
}