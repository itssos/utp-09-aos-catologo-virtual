<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/config/constants.php';
require __DIR__ . '/../src/config/helpers.php';

use App\controllers\AuthController;
use App\controllers\ProductController;
use App\Middleware\AuthMiddleware;

// Rutas simples
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch ("{$method} {$path}") {

    // Vistas públicas
    case 'GET '.route('register'):
        if (!can('create_user')) (new AuthController())->forbidden();
        include __DIR__ . '/../src/views/auth/register.php';
        break;

    case 'POST '.route('register'):
        if (!(new AuthMiddleware())->handle()) exit;
        (new AuthController())->register();
        break;

    case 'GET '.route('login'):
        include __DIR__ . '/../src/views/auth/login.php';
        break;

    case 'POST '.route('login'):
        (new AuthController())->login();
        break;

    case 'GET '.route('logout'):
        (new AuthController())->logout();
        break;
    
    case 'GET '.route('home'):
        (new ProductController())->list();
        break;

    // productos
    case 'GET ' . route('product_store'):
        if (!can('view_product')) (new AuthController())->forbidden();
        (new ProductController())->adminList();
        break;

    case 'GET ' . route('product_create'):
        if (!can('create_product')) (new AuthController())->forbidden();
        (new ProductController())->create();
        break;

    case 'POST ' . route('product_store'):
        if (!can('view_product')) (new AuthController())->forbidden();
        (new ProductController())->store();
        break;

    case 'GET ' . route('product_edit'):
        if (!can('edit_product')) (new AuthController())->forbidden();
        (new ProductController())->edit();
        break;

    case 'POST ' . route('product_update'):
        if (!can('edit_product')) (new AuthController())->forbidden();
        (new ProductController())->update();
        break;

    case 'POST ' . route('product_delete'):
        if (!can('delete_product')) (new AuthController())->forbidden();
        (new ProductController())->delete();
        break;



    default:
        (new AuthController())->notFound();
        break;
}
