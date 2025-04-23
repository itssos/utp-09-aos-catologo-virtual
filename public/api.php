<?php
declare(strict_types=1);

$allowedOrigin = 'http://localhost:8000';
header('Access-Control-Allow-Origin: ' . $allowedOrigin);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/config/constants.php';

// API REST CONTROLLERS ===================================
use App\Api\ApiAuthController;
use App\Api\ApiCategoryController;
use App\Api\ApiPermissionController;
use App\Api\ApiProductController;
use App\Api\ApiUserController;

// VIEWS CONTROLLERS ======================================
use App\Controllers\ProductController;

// UTILS ==================================================
use App\Config\Database;
use App\Routing\Router;

$pdo = Database::getConnection();
require __DIR__ . '/../src/config/helpers.php';
$router = new Router();

// API ROUTES =============================================
$apiCategoryController = new ApiCategoryController($pdo);
$apiCategoryController->registerRoutes($router);

$apiUserController = new ApiUserController($pdo);
$apiUserController->registerRoutes($router);

$apiAuthController = new ApiAuthController($pdo);
$apiAuthController->registerRoutes($router);

$apiPermController = new ApiPermissionController($pdo);
$apiPermController->registerRoutes($router);

$apiProductController = new ApiProductController($pdo);
$apiProductController->registerRoutes($router);

// DISPATCH ===============================================
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($method, $path, $pdo);