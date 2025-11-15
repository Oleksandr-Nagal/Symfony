<?php

namespace App\Service;

use App\Entity\Movie;
use App\Entity\Genre;
use App\Entity\Actor;
use App\Entity\Director;
use Doctrine\ORM\EntityManagerInterface;

class DirectorService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function createMovie(string $title, string $description, int $duration, \DateTime $releaseDate, array $genres = [], array $actors = [], ?Director $director = null): Movie
    {
        $movie = new Movie();
        $movie->setTitle($title);
        $movie->setDescription($description);
        $movie->setDuration($duration);
        $movie->setReleaseDate($releaseDate);
        if ($director) $movie->setDirector($director);
        foreach ($genres as $genre) $movie->addGenre($genre);
        foreach ($actors as $actor) $movie->addActor($actor);
        $this->em->persist($movie);
        $this->em->flush();
        return $movie;
    }
}
