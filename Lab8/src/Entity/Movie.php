<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;

use App\Controller\Api\MovieUppercaseTitleAction;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['movie:read']],
    denormalizationContext: ['groups' => ['movie:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Patch(),
        new Delete(),

        // ==== CUSTOM ACTION ====
        new Get(
            uriTemplate: '/movies/{id}/uppercase_title',
            uriVariables: [
                'id' => [
                    'from_class' => Movie::class,
                    'from_property' => 'id'
                ]
            ],
            controller: MovieUppercaseTitleAction::class,
            read: true,
            deserialize: false,
            name: 'movie_uppercase_title',
            openapiContext: [
                'summary' => 'Returns movie title in uppercase'
            ]
        ),
    ]
)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['movie:read', 'session:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['movie:read', 'movie:write'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['movie:read', 'movie:write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Groups(['movie:read', 'movie:write'])]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull]
    #[Groups(['movie:read', 'movie:write'])]
    private ?\DateTimeInterface $releaseDate = null;

    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'movies')]
    #[Groups(['movie:read', 'movie:write'])]
    private Collection $genres;

    #[ORM\ManyToMany(targetEntity: Actor::class, inversedBy: 'movies')]
    #[Groups(['movie:read', 'movie:write'])]
    private Collection $actors;

    #[ORM\ManyToOne(inversedBy: 'movies')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['movie:read', 'movie:write'])]
    private ?Director $director = null;

    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'movie')]
    private Collection $sessions;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->actors = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }

    // ======= GETTERS / SETTERS =======

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }

    public function getDuration(): ?int { return $this->duration; }
    public function setDuration(int $duration): static { $this->duration = $duration; return $this; }

    public function getReleaseDate(): ?\DateTimeInterface { return $this->releaseDate; }
    public function setReleaseDate(\DateTimeInterface $date): static { $this->releaseDate = $date; return $this; }

    public function getGenres(): Collection { return $this->genres; }
    public function getActors(): Collection { return $this->actors; }
    public function getDirector(): ?Director { return $this->director; }
    public function setDirector(Director $d): static { $this->director = $d; return $this; }

    public function getSessions(): Collection { return $this->sessions; }
}
