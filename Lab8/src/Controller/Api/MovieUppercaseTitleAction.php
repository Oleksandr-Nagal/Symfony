<?php

namespace App\Controller\Api;

use App\Entity\Movie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MovieUppercaseTitleAction extends AbstractController
{
    public function __invoke(Movie $movie): JsonResponse
    {
        return new JsonResponse([
            'id' => $movie->getId(),
            'original_title' => $movie->getTitle(),
            'uppercase_title' => mb_strtoupper($movie->getTitle()),
        ]);
    }
}
