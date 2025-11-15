<?php

namespace App\Service;

use App\Entity\Genre;
use Doctrine\ORM\EntityManagerInterface;

class GenreService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function createGenre(string $name): Genre
    {
        $genre = new Genre();
        $genre->setName($name);
        $this->em->persist($genre);
        $this->em->flush();
        return $genre;
    }
}
