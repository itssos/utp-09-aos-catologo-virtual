<?php
// public/index.php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/config/constants.php';

use App\controllers\AuthController;
use App\controllers\ProductController;
use App\middleware\AuthMiddleware;

// Rutas simples
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch ("{$method} {$path}") {

    // Vistas públicas
    case 'GET /register':
        include __DIR__ . '/../src/views/auth/register.php';
        break;

    case 'POST /register':
        (new AuthController())->register();
        break;

    case 'GET /login':
        include __DIR__ . '/../src/views/auth/login.php';
        break;

    case 'POST /login':
        (new AuthController())->login();
        break;

    case 'GET /logout':
        (new AuthController())->logout();
        break;

    // Rutas protegidas
    case 'GET /products':
        if (!(new AuthMiddleware())->handle()) exit;
        (new ProductController())->list();
        break;

    default:
        http_response_code(404);
        echo "Página no encontrada.";
        break;
}
