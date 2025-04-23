<?php

declare(strict_types=1);

namespace App\Controllers;

use Exception;

class AuthController
{

    public function __construct()
    {

    }

    public function registerRoutes($router): void
    {
        $router->get('/admin/login', fn($p) => $this->showLogin());
        $router->post('/admin/login', fn($p) => $this->login());
        $router->get('/admin/logout', fn($p) => $this->logout());
    }

    public function showLogin(): void
    {
        header('Content-Type: text/html; charset=utf-8');
        include VIEWS_PATH . '/auth/login.php';
    }

    public function login(): void
    {
        header('Content-Type: text/html; charset=utf-8');
        try {
            $body = json_decode(file_get_contents('php://input'), true);
            // Si no vienen JSON, tomamos de $_POST
            if (!is_array($body)) {
                $body = $_POST;
            }

            // echo json_encode($body);

            httpRequest('POST', '/api/login', $body);
            setFlash('success', 'Bienvenido');
            header('Location: ' . route('home'));
        } catch (Exception $e) {
            setFlash('error', $e->getMessage());
            header('Location: ' . route('login'));
        }
        exit;
    }

    public function logout(): void
    {
        header('Content-Type: text/html; charset=utf-8');
        try {
            httpRequest('POST', '/api/logout');
            setFlash('success', 'Sesión cerrada');
        } catch (Exception $e) {
            setFlash('error', 'No se pudo cerrar sesión');
        }
        header('Location: ' . route('login'));
        exit;
    }
}
