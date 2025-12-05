<?php

namespace App\Controller\Api;

use App\Entity\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class SessionTicketsCountAction extends AbstractController
{
    public function __invoke(Session $session): JsonResponse
    {
        return new JsonResponse([
            'session_id' => $session->getId(),
            'tickets_count' => $session->getTickets()->count(),
        ]);
    }
}
