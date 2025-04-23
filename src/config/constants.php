<?php
define('DB_HOST',     '127.0.0.1');
define('DB_NAME',     'libreria_jesus_mi_amigo');
define('DB_USER',     'root');
define('DB_PASS',     'root');
define('JWT_SECRET',  '08725e547914eb52b7abba9bdfe7be4a2a5e6b8d9ca977d84a20816ff4f5023c');
define('JWT_ISSUER',  'http://localhost:8001');

define('BASE_URL', '');
define('API_BASE_URL', 'http://localhost:8001');

define('SRC_PATH', __DIR__ . '/../');
define('MODELS_PATH', SRC_PATH . 'models/');
define('API_PATH', SRC_PATH . 'api/');
define('VIEWS_PATH', SRC_PATH . 'views/');

define('NO_IMAGE', '/assets/image/no-image.jpg');

// --------------------------------------------------
// RUTAS DE LA APLICACIÓN
// --------------------------------------------------
const ROUTES = [
    'home'       => '/',
    'login'      => '/admin/login',
    'register'   => '/admin/register',
    'logout'     => '/admin/logout',
    'dashboard'  => '/admin/dashboard',
    // Productos
    'product_create'   => '/admin/productos/create',  // GET
    'product_store'    => '/admin/productos',         // POST
    'product_edit'     => '/admin/productos/edit',    // GET  ?id=
    'product_update'   => '/admin/productos/update',  // POST
    'product_delete'   => '/admin/productos/delete',  // POST
];

// --------------------------------------------------
// PERMISOS (deben coincidir con tu tabla `permissions.name`)
// --------------------------------------------------
const PERMISSIONS = [
    'create_user'       => 'create_user',
    'view_dashboard' => 'view_dashboard',
    'create_product' => 'create_product',
    'view_product'     => 'view_product',
    'edit_product'     => 'edit_product',
    'delete_product'   => 'delete_product',
    'view_user'        => 'view_user',
];
