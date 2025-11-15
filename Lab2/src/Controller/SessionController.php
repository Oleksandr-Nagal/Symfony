<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\SessionRepository;
use App\Entity\Movie;
use App\Entity\Hall;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sessions')]
class SessionController extends AbstractController
{
    public function __construct(private SessionRepository $repo) {}

    #[Route('', name: 'session_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->repo->findAll(), 200, [], ['groups' => 'session:read']);
    }

    #[Route('/{id}', name: 'session_show', methods: ['GET'])]
    public function show(Session $session): JsonResponse
    {
        return $this->json($session, 200, [], ['groups' => 'session:read']);
    }

    #[Route('', name: 'session_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $movie = $em->getRepository(Movie::class)->find($data['movie_id']);
        $hall = $em->getRepository(Hall::class)->find($data['hall_id']);

        if (!$movie || !$hall) {
            return $this->json(['error' => 'Movie or Hall not found'], 404);
        }

        $session = new Session();
        $session->setStartTime(new \DateTime($data['startTime']));
        $session->setPrice($data['price']);
        $session->setMovie($movie);
        $session->setHall($hall);
        $em->persist($session);
        $em->flush();

        return $this->json($session, 201, [], ['groups' => 'session:read']);
    }

    #[Route('/{id}', name: 'session_update', methods: ['PUT','PATCH'])]
    public function update(Request $request, Session $session, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['startTime'])) $session->setStartTime(new \DateTime($data['startTime']));
        if (isset($data['price'])) $session->setPrice($data['price']);

        if (isset($data['movie_id'])) {
            $movie = $em->getRepository(Movie::class)->find($data['movie_id']);
            if (!$movie) return $this->json(['error' => 'Movie not found'], 404);
            $session->setMovie($movie);
        }

        if (isset($data['hall_id'])) {
            $hall = $em->getRepository(Hall::class)->find($data['hall_id']);
            if (!$hall) return $this->json(['error' => 'Hall not found'], 404);
            $session->setHall($hall);
        }

        $em->flush();
        return $this->json($session, 200, [], ['groups' => 'session:read']);
    }

    #[Route('/{id}', name: 'session_delete', methods: ['DELETE'])]
    public function delete(Session $session, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($session);
        $em->flush();

        return $this->json(null, 204);
    }
}