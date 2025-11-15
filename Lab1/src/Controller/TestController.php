<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/items')]
class TestController extends AbstractController
{
    private static array $items = [
        1 => ['id' => 1, 'name' => 'Item A', 'description' => 'First static item'],
        2 => ['id' => 2, 'name' => 'Item B', 'description' => 'Second static item'],
    ];

    private static int $nextId = 3;

    /**
     * READ: Отримати список усіх елементів
     *
     * Маршрут: GET /api/items
     */
    #[Route('', name: 'app_item_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        // Повертаємо всі елементи зі сховища
        return new JsonResponse(array_values(self::$items));
    }

    /**
     * READ: Отримати один елемент за ID
     * Маршрут: GET /api/items/{id}
     */
    #[Route('/{id}', name: 'app_item_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        if (!isset(self::$items[$id])) {
            return new JsonResponse(['error' => "Item with ID $id not found"], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(self::$items[$id]);
    }

    /**
     * CREATE: Створити новий елемент
     * Маршрут: POST /api/items
     * Очікує JSON body: {"name": "New Item Name", "description": "Optional details"}
     */
    #[Route('', name: 'app_item_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name'])) {
            return new JsonResponse(['error' => 'The "name" field is required'], Response::HTTP_BAD_REQUEST);
        }

        $newId = self::$nextId++;

        $newItem = [
            'id' => $newId,
            'name' => $data['name'],
            'description' => $data['description'] ?? 'No description provided',
        ];

        self::$items[$newId] = $newItem;

        return new JsonResponse($newItem, Response::HTTP_CREATED);
    }

    /**
     * UPDATE: Оновити існуючий елемент
     * Маршрут: PUT /api/items/{id}
     * Очікує JSON body: {"name": "Updated Name", "description": "New details"}
     */
    #[Route('/{id}', name: 'app_item_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        if (!isset(self::$items[$id])) {
            return new JsonResponse(['error' => "Item with ID $id not found"], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Оновлюємо поля, якщо вони надані
        if (isset($data['name'])) {
            self::$items[$id]['name'] = $data['name'];
        }
        if (isset(self::$items[$id]['description'], $data['description'])) {
            self::$items[$id]['description'] = $data['description'];
        }

        return new JsonResponse(self::$items[$id]);
    }

    /**
     * DELETE: Видалити елемент
     * Маршрут: DELETE /api/items/{id}
     */
    #[Route('/{id}', name: 'app_item_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        if (!isset(self::$items[$id])) {
            return new JsonResponse(['error' => "Item with ID $id not found"], Response::HTTP_NOT_FOUND);
        }

        $deletedItem = self::$items[$id];
        unset(self::$items[$id]);

        return new JsonResponse(['message' => "Item with ID $id deleted successfully", 'deleted_item' => $deletedItem], Response::HTTP_OK);
    }
}
