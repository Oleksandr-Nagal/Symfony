<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;

use App\Controller\Api\SessionTicketsCountAction;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['session:read']],
    denormalizationContext: ['groups' => ['session:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Patch(),
        new Delete(),

        new Get(
            uriTemplate: '/sessions/{id}/tickets_count',
            uriVariables: [
                'id' => [
                    'from_class' => Session::class,
                    'from_property' => 'id'
                ]
            ],
            controller: SessionTicketsCountAction::class,
            read: true,
            deserialize: false,
            name: 'session_tickets_count',
            openapiContext: [
                'summary' => 'Returns number of tickets for this session'
            ]
        ),
    ]
)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['session:read', 'movie:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Groups(['session:read', 'session:write'])]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[Groups(['session:read', 'session:write'])]
    private ?string $price = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['session:read', 'session:write'])]
    private ?Hall $hall = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['session:read', 'session:write'])]
    private ?Movie $movie = null;

    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'session')]
    private Collection $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getStartTime(): ?\DateTimeInterface { return $this->startTime; }
    public function setStartTime(\DateTimeInterface $t): static { $this->startTime = $t; return $this; }

    public function getPrice(): ?string { return $this->price; }
    public function setPrice(string $price): static { $this->price = $price; return $this; }

    public function getHall(): ?Hall { return $this->hall; }
    public function setHall(Hall $hall): static { $this->hall = $hall; return $this; }

    public function getMovie(): ?Movie { return $this->movie; }
    public function setMovie(Movie $m): static { $this->movie = $m; return $this; }

    public function getTickets(): Collection { return $this->tickets; }
}
