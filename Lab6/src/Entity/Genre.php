<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection; // Новий імпорт для колекції
use ApiPlatform\Metadata\Get;           // Новий імпорт для окремого елемента
use ApiPlatform\Metadata\Post;          // Новий імпорт для POST
use ApiPlatform\Metadata\Patch;         // Новий імпорт для PATCH
use ApiPlatform\Metadata\Delete;        // Новий імпорт для DELETE

#[ORM\Entity(repositoryClass: GenreRepository::class)]
#[ORM\Table(name: 'genre')]
#[ApiResource(
    // Використовуємо єдиний масив "operations"
    operations: [
        // GET COLLECTION (Отримати всі)
        new GetCollection(normalizationContext: ['groups' => ['genre:list']]),

        // POST (Створити)
        new Post(
            denormalizationContext: ['groups' => ['genre:write']],
            validationContext: ['groups' => ['Default', 'create']],
        ),

        // GET ITEM (Отримати один)
        new Get(),

        // PATCH (Оновити частково)
        new Patch(
            denormalizationContext: ['groups' => ['genre:write']],
        ),

        // DELETE (Видалити)
        new Delete(),
    ],
    // Загальний контекст нормалізації для GET
    normalizationContext: ['groups' => ['genre:read']],
)]
class Genre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['genre:read', 'movie:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['genre:read', 'genre:list', 'genre:write'])]
    #[Assert\NotBlank(message: "Назва жанру не може бути пустою.")]
    #[Assert\Length(min: 2, max: 255, minMessage: "Назва має бути довшою за 2 символи.")]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Movie::class, inversedBy: 'genres')]
    private Collection $movies;

    public function __construct()
    {
        $this->movies = new ArrayCollection();
    }

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

    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movie $movie): static
    {
        if (!$this->movies->contains($movie)) {
            $this->movies->add($movie);
        }

        return $this;
    }

    public function removeMovie(Movie $movie): static
    {
        $this->movies->removeElement($movie);

        return $this;
    }
}