<?php

namespace App\Entity;

use App\Repository\StaffRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;

#[ApiResource(
    normalizationContext: ['groups' => ['staff:read']],
    denormalizationContext: ['groups' => ['staff:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Patch(),
        new Delete(),
    ]
)]
#[ORM\Entity(repositoryClass: StaffRepository::class)]
class Staff
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['staff:read', 'hall:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['staff:read', 'staff:write'])]
    #[Assert\NotBlank(message: 'Name cannot be blank.')]
    #[Assert\Length(min: 2)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    #[Groups(['staff:read', 'staff:write'])]
    #[Assert\NotBlank(message: 'Position cannot be blank.')]
    private ?string $position = null;

    #[ORM\ManyToOne(inversedBy: 'staff')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['staff:read', 'staff:write'])]
    #[Assert\NotNull(message: 'Hall is required.')]
    private ?Hall $hall = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getHall(): ?Hall
    {
        return $this->hall;
    }

    public function setHall(?Hall $hall): static
    {
        $this->hall = $hall;

        return $this;
    }
}
