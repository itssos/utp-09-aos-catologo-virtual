<?php

declare(strict_types=1);

namespace App\Api;

use App\Routing\Router;
use App\Service\UserService;
use App\Service\PermissionService;
use App\Models\User;
use App\Models\Permission;
use PDO;
use Exception;

class ApiUserController
{
    private UserService $userService;
    private PermissionService $permissionService;

    public function __construct(PDO $db)
    {
        $this->permissionService = new PermissionService(new Permission($db));
        $this->userService       = new UserService(new User($db), $this->permissionService);
    }

    public function registerRoutes(Router $router): void
    {
        // Rutas de usuarios
        $router->get('/api/users', fn($p) => $this->index(), 'view_user');
        $router->post('/api/users', fn($p) => $this->store(), 'create_user');
        $router->get('/api/users/{id}', fn($p) => $this->show((int)$p['id']), 'view_user');
        $router->put('/api/users/{id}', fn($p) => $this->update((int)$p['id']), 'edit_user');
        $router->delete('/api/users/{id}', fn($p) => $this->destroy((int)$p['id']), 'delete_user');

        $router->get('/api/users/{id}/permissions', fn($p) => $this->userPermissions((int)$p['id']), 'view_permission');
    }

    public function index(): void
    {
        header('Content-Type: application/json');
        try {
            $data = $this->userService->getAll();
            echo json_encode(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            $this->errorResponse($e);
        }
    }

    public function show(int $id): void
    {
        header('Content-Type: application/json');
        try {
            $user = $this->userService->getById($id);
            echo json_encode(['status' => 'success', 'data' => $user]);
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
            $id   = $this->userService->create($body);
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
            $perms = $body['permissions'] ?? null;
            $this->userService->updateWithPermissions($id, $body, $perms);
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            http_response_code(http_response_code() ?: 400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function destroy(int $id): void
    {
        header('Content-Type: application/json');
        try {
            $this->userService->delete($id);
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            $this->errorResponse($e);
        }
    }

    public function userPermissions(int $userId): void
    {
        header('Content-Type: application/json');
        try {
            $perms = $this->permissionService->getByUserId($userId);
            echo json_encode(['status' => 'success', 'data' => $perms]);
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
