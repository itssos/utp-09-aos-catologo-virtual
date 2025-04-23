<?php

declare(strict_types=1);

namespace App\Api;

use App\Service\PermissionService;
use App\Routing\Router;
use App\Models\Permission;
use PDO;
use Exception;

class ApiPermissionController
{
    private PermissionService $permissionService;

    public function __construct(PDO $db)
    {
        $this->permissionService = new PermissionService(new Permission($db));
    }

    /**
     * Registra rutas CRUD para permisos.
     */
    public function registerRoutes(Router $router): void
    {
        $router->get('/api/permissions', fn($p) => $this->index());
        $router->get('/api/permissions/{id}', fn($p) => $this->show((int)$p['id']), 'view_permission');
        $router->post('/api/permissions', fn($p) => $this->store(), 'create_permission');
        $router->put('/api/permissions/{id}', fn($p) => $this->update((int)$p['id']), 'edit_permission');
        $router->delete('/api/permissions/{id}', fn($p) => $this->destroy((int)$p['id']), 'delete_permission');
    }

    public function index(): void
    {
        header('Content-Type: application/json');
        try {
            $perms = $this->permissionService->getAll();
            echo json_encode(['status' => 'success', 'data' => $perms]);
        } catch (Exception $e) {
            $this->errorResponse($e);
        }
    }

    public function show(int $id): void
    {
        header('Content-Type: application/json');
        try {
            $perm = $this->permissionService->getById($id);
            echo json_encode(['status' => 'success', 'data' => $perm]);
        } catch (Exception $e) {
            http_response_code(404);
            $this->errorResponse($e);
        }
    }

    public function store(): void
    {
        header('Content-Type: application/json');
        try {
            $body = json_decode(file_get_contents('php://input'), true) ?: [];
            $id = $this->permissionService->create($body);
            http_response_code(201);
            echo json_encode(['status' => 'success', 'id' => $id]);
        } catch (Exception $e) {
            $this->errorResponse($e);
        }
    }

    public function update(int $id): void
    {
        header('Content-Type: application/json');
        try {
            $body = json_decode(file_get_contents('php://input'), true) ?: [];
            $this->permissionService->update($id, $body);
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            $this->errorResponse($e);
        }
    }

    public function destroy(int $id): void
    {
        header('Content-Type: application/json');
        try {
            $this->permissionService->delete($id);
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            $this->errorResponse($e);
        }
    }

    private function errorResponse(Exception $e): void
    {
        http_response_code(http_response_code() ?: 400);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
