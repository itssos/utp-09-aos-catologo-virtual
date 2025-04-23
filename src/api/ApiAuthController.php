<?php

declare(strict_types=1);

namespace App\Api;

use App\config\JwtHandler;
use App\Models\User;
use App\Service\UserService;
use App\Routing\Router;
use PDO;
use Exception;

class ApiAuthController
{
    private UserService $userService;

    public function __construct(PDO $db)
    {
        $this->userService = new UserService(new User($db));
    }

    /**
     * Registra rutas de autenticación.
     */
    public function registerRoutes(Router $router): void
    {
        $router->post('/api/login', fn($p) => $this->login());
        $router->post('/api/logout', fn($p) => $this->logout());
    }

    /**
     * Login: valida credenciales y guarda JSON de usuario en cookie.
     */
    public function login(): void
    {
        header('Content-Type: application/json');
        try {
            $body = json_decode(file_get_contents('php://input'), true) ?: [];
            if (empty($body['username']) || empty($body['password'])) {
                throw new Exception('Credenciales inválidas');
            }
            $user = $this->userService->getByUsername($body['username']);
            if (!password_verify($body['password'], $user->getPasswordHash())) {
                throw new Exception('Credenciales inválidas');
            }
            // Guardar usuario en cookie
            $jwt = (new JwtHandler())->generateToken($user->getId());
            setcookie('token', $jwt, time() + 3600, '/', '', false, true);
            echo json_encode(['status' => 'success', 'data' => $user]);
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Logout: elimina la cookie.
     */
    public function logout(): void
    {
        header('Content-Type: application/json');
        setcookie('token', '', time() - 3600, '/', '', false, true);
        echo json_encode(['status' => 'success', 'message' => 'Sesión cerrada']);
    }
}
