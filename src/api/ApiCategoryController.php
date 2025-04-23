<?php

declare(strict_types=1);

namespace App\Api;

use App\Service\CategoryService;
use App\Models\Category;
use App\Routing\Router;
use PDO;
use Exception;

class ApiCategoryController
{
    private CategoryService $service;

    public function __construct(PDO $db)
    {
        // Inyectamos el modelo vía el servicio
        $this->service = new CategoryService(new Category($db));
        header('Content-Type: application/json; charset=utf-8');
    }

    public function registerRoutes(Router $router): void
    {
        $router->get('/api/category', fn($p) => $this->index(), 'view_category');
        $router->post('/api/category', fn($p) => $this->store(), 'create_category');

        $router->get('/api/category/{id}', fn($p) => $this->show((int)$p['id']), 'view_category');
        $router->put('/api/category/{id}', fn($p) => $this->update((int)$p['id']), 'edit_category');
        $router->delete('/api/category/{id}', fn($p) => $this->destroy((int)$p['id']), 'delete_category');
    }


    public function index(): void
    {
        try {
            $data = $this->service->getAll();
            http_response_code(200);
            echo json_encode(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            $this->errorResponse($e);
        }
    }

    public function show(int $id): void
    {
        try {
            $item = $this->service->getById($id);
            http_response_code(200);
            echo json_encode(['status' => 'success', 'data' => $item]);
        } catch (Exception $e) {
            // Si no existe, devolvemos 404
            $code = $e->getMessage() === 'Categoría no encontrada' ? 404 : 400;
            http_response_code($code);
            $this->errorResponse($e);
        }
    }

    public function store(): void
    {
        try {
            $body = json_decode(file_get_contents('php://input'), true);
            if (!is_array($body)) {
                throw new Exception('Datos inválidos');
            }

            $newId = $this->service->create($body);
            http_response_code(201);
            echo json_encode(['status' => 'success', 'id' => $newId]);
        } catch (Exception $e) {
            $this->errorResponse($e);
        }
    }

    public function update(int $id): void
    {
        try {
            $body = json_decode(file_get_contents('php://input'), true);
            if (!is_array($body)) {
                throw new Exception('Datos inválidos');
            }

            $this->service->update($id, $body);
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Categoría actualizada']);
        } catch (Exception $e) {
            $this->errorResponse($e);
        }
    }

    public function destroy(int $id): void
    {
        try {
            $this->service->delete($id);
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Categoría eliminada']);
        } catch (Exception $e) {
            $this->errorResponse($e);
        }
    }

    private function errorResponse(Exception $e): void
    {
        http_response_code(http_response_code() ?: 400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
        ]);
    }
}
