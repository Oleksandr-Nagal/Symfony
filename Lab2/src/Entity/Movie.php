<?php
//
//namespace App\Entity;
//
//use App\Repository\MovieRepository;
//use Doctrine\Common\Collections\ArrayCollection;
//use Doctrine\Common\Collections\Collection;
//use Doctrine\DBAL\Types\Types;
//use Doctrine\ORM\Mapping as ORM;
//
//#[ORM\Entity(repositoryClass: MovieRepository::class)]
//class Movie
//{
//    #[ORM\Id]
//    #[ORM\GeneratedValue]
//    #[ORM\Column]
//    private ?int $id = null;
//
//    #[ORM\Column(length: 255)]
//    private ?string $title = null;
//
//    #[ORM\Column(type: Types::TEXT)]
//    private ?string $description = null;
//
//    #[ORM\Column]
//    private ?int $duration = null;
//
//    #[ORM\Column(type: Types::DATE_MUTABLE)]
//    private ?\DateTimeInterface $releaseDate = null;
//
//    #[ORM\ManyToMany(targetEntity: Genre::class, mappedBy: 'movies')]
//    private Collection $genres;
//
//    #[ORM\ManyToMany(targetEntity: Actor::class, mappedBy: 'movies')]
//    private Collection $actors;
//
//    #[ORM\ManyToOne(inversedBy: 'movies')]
//    #[ORM\JoinColumn(nullable: false)]
//    private ?Director $director = null;
//
//    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'movie')]
//    private Collection $sessions;
//
//    public function __construct()
//    {
//        $this->genres = new ArrayCollection();
//        $this->actors = new ArrayCollection();
//        $this->sessions = new ArrayCollection();
//    }
//
//    public function getId(): ?int
//    {
//        return $this->id;
//    }
//
//    public function getTitle(): ?string
//    {
//        return $this->title;
//    }
//
//    public function setTitle(string $title): static
//    {
//        $this->title = $title;
//
//        return $this;
//    }
//
//    public function getDescription(): ?string
//    {
//        return $this->description;
//    }
//
//    public function setDescription(string $description): static
//    {
//        $this->description = $description;
//
//        return $this;
//    }
//
//    public function getDuration(): ?int
//    {
//        return $this->duration;
//    }
//
//    public function setDuration(int $duration): static
//    {
//        $this->duration = $duration;
//
//        return $this;
//    }
//
//    public function getReleaseDate(): ?\DateTimeInterface
//    {
//        return $this->releaseDate;
//    }
//
//    public function setReleaseDate(\DateTimeInterface $releaseDate): static
//    {
//        $this->releaseDate = $releaseDate;
//
//        return $this;
//    }
//
//    /**
//     * @return Collection<int, Genre>
//     */
//    public function getGenres(): Collection
//    {
//        return $this->genres;
//    }
//
//    public function addGenre(Genre $genre): static
//    {
//        if (!$this->genres->contains($genre)) {
//            $this->genres->add($genre);
//            $genre->addMovie($this);
//        }
//
//        return $this;
//    }
//
//    public function removeGenre(Genre $genre): static
//    {
//        if ($this->genres->removeElement($genre)) {
//            $genre->removeMovie($this);
//        }
//
//        return $this;
//    }
//
//    /**
//     * @return Collection<int, Actor>
//     */
//    public function getActors(): Collection
//    {
//        return $this->actors;
//    }
//
//    public function addActor(Actor $actor): static
//    {
//        if (!$this->actors->contains($actor)) {
//            $this->actors->add($actor);
//            $actor->addMovie($this);
//        }
//
//        return $this;
//    }
//
//    public function removeActor(Actor $actor): static
//    {
//        if ($this->actors->removeElement($actor)) {
//            $actor->removeMovie($this);
//        }
//
//        return $this;
//    }
//
//    public function getDirector(): ?Director
//    {
//        return $this->director;
//    }
//
//    public function setDirector(?Director $director): static
//    {
//        $this->director = $director;
//
//        return $this;
//    }
//
//    /**
//     * @return Collection<int, Session>
//     */
//    public function getSessions(): Collection
//    {
//        return $this->sessions;
//    }
//
//    public function addSession(Session $session): static
//    {
//        if (!$this->sessions->contains($session)) {
//            $this->sessions->add($session);
//            $session->setMovie($this);
//        }
//
//        return $this;
//    }
//
//    public function removeSession(Session $session): static
//    {
//        if ($this->sessions->removeElement($session)) {
//            // set the owning side to null (unless already changed)
//            if ($session->getMovie() === $this) {
//                $session->setMovie(null);
//            }
//        }
//
//        return $this;
//    }
//}


namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['movie:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['movie:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['movie:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['movie:read'])]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['movie:read'])] // <-- Додано групу
    private ?\DateTimeInterface $releaseDate = null;


    #[ORM\ManyToMany(targetEntity: Genre::class, mappedBy: 'movies')]
    private Collection $genres;

    #[ORM\ManyToMany(targetEntity: Actor::class, mappedBy: 'movies')]
    private Collection $actors;

    #[ORM\ManyToOne(inversedBy: 'movies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Director $director = null;

    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'movie')]
    private Collection $sessions;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->actors = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): static
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
            $genre->addMovie($this);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): static
    {
        if ($this->genres->removeElement($genre)) {
            $genre->removeMovie($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Actor>
     */
    public function getActors(): Collection
    {
        return $this->actors;
    }

    public function addActor(Actor $actor): static
    {
        if (!$this->actors->contains($actor)) {
            $this->actors->add($actor);
            $actor->addMovie($this);
        }

        return $this;
    }

    public function removeActor(Actor $actor): static
    {
        if ($this->actors->removeElement($actor)) {
            $actor->removeMovie($this);
        }

        return $this;
    }

    public function getDirector(): ?Director
    {
        return $this->director;
    }

    public function setDirector(?Director $director): static
    {
        $this->director = $director;

        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): static
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setMovie($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getMovie() === $this) {
                $session->setMovie(null);
            }
        }

        return $this;
    }
}