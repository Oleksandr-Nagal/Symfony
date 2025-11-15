<?php
//
//namespace App\Controller;
//
//use App\Entity\Movie;
//use App\Repository\MovieRepository;
//use App\Service\MovieService;
//use App\Service\ValidatorService;
//use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\Routing\Annotation\Route;
//
//#[Route('/movies')]
//class MovieController extends AbstractController
//{
//    public function __construct(private MovieRepository $repo, private MovieService $service, private ValidatorService $validator) {}
//
//    #[Route('', name: 'movie_index', methods: ['GET'])]
//    public function index(): JsonResponse
//    {
//        return $this->json($this->repo->findAll());
//    }
//
//    #[Route('/{id}', name: 'movie_show', methods: ['GET'])]
//    public function show(Movie $movie): JsonResponse
//    {
//        return $this->json($movie);
//    }
//
//    #[Route('', name: 'movie_create', methods: ['POST'])]
//    public function create(Request $request): JsonResponse
//    {
//        $data = json_decode($request->getContent(), true);
//        $errors = $this->validator->validateMovie($data);
//        if ($errors) return $this->json(['errors' => $errors], 400);
//        $movie = $this->service->createMovie($data['title'], $data['description'], $data['duration'], new \DateTime($data['releaseDate']));
//        return $this->json($movie);
//    }
//
//    #[Route('/{id}', name: 'movie_update', methods: ['PUT','PATCH'])]
//    public function update(Request $request, Movie $movie, EntityManagerInterface $em): JsonResponse
//    {
//        $data = json_decode($request->getContent(), true);
//        if (isset($data['title'])) $movie->setTitle($data['title']);
//        if (isset($data['description'])) $movie->setDescription($data['description']);
//        if (isset($data['duration'])) $movie->setDuration($data['duration']);
//        if (isset($data['releaseDate'])) $movie->setReleaseDate(new \DateTime($data['releaseDate']));
//        $em->flush();
//        return $this->json($movie);
//    }
//
//    #[Route('/{id}', name: 'movie_delete', methods: ['DELETE'])]
//    public function delete(Movie $movie, EntityManagerInterface $em): JsonResponse
//    {
//        $em->remove($movie);
//        $em->flush();
//        return $this->json(['deleted' => true]);
//    }
//}


namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Service\MovieService;
use App\Service\ValidatorService;

// Переконайтесь, що цей сервіс існує
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/movies')]
class MovieController extends AbstractController
{
    // Використовуємо PHP 8+ властивості конструктора
    public function __construct(
        private MovieRepository  $repo,
        private MovieService     $service,
        private ValidatorService $validator
    )
    {
    }

    /**
     * Отримує список всіх фільмів
     */
    #[Route('', name: 'movie_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        // Додано 'groups' => 'movie:read' для уникнення циклічних посилань
        return $this->json($this->repo->findAll(), 200, [], ['groups' => 'movie:read']);
    }

    /**
     * Отримує один фільм за ID
     */
    #[Route('/{id}', name: 'movie_show', methods: ['GET'])]
    public function show(Movie $movie): JsonResponse
    {
        // Додано 'groups' => 'movie:read'
        return $this->json($movie, 200, [], ['groups' => 'movie:read']);
    }

    /**
     * Створює новий фільм
     */
    #[Route('', name: 'movie_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Припускаємо, що ValidatorService має метод validateMovie,
        // який ви створили
        $errors = $this->validator->validateMovie($data);
        if ($errors) {
            return $this->json(['errors' => $errors], 400); // 400 Bad Request
        }

        // Припускаємо, що MovieService повертає створений об'єкт Movie
        $movie = $this->service->createMovie(
            $data['title'],
            $data['description'],
            $data['duration'],
            new \DateTime($data['releaseDate'])
        );

        // Додано 'groups' => 'movie:read'
        // Повертаємо 201 Created зі створеним об'єктом
        return $this->json($movie, 201, [], ['groups' => 'movie:read']);
    }

    /**
     * Оновлює існуючий фільм
     */
    #[Route('/{id}', name: 'movie_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Movie $movie, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Тут можна додати валідацію, аналогічно до create

        if (isset($data['title'])) $movie->setTitle($data['title']);
        if (isset($data['description'])) $movie->setDescription($data['description']);
        if (isset($data['duration'])) $movie->setDuration($data['duration']);
        if (isset($data['releaseDate'])) $movie->setReleaseDate(new \DateTime($data['releaseDate']));

        $em->flush();

        // Додано 'groups' => 'movie:read'
        return $this->json($movie, 200, [], ['groups' => 'movie:read']);
    }

    /**
     * Видаляє фільм
     */
    #[Route('/{id}', name: 'movie_delete', methods: ['DELETE'])]
    public function delete(Movie $movie, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($movie);
        $em->flush();

        // Повертаємо 204 No Content, тіло відповіді буде порожнім
        return $this->json(null, 204);
    }
}