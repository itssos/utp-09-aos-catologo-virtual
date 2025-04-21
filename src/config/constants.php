<?php
define('DB_HOST',     '127.0.0.1');
define('DB_NAME',     'libreria_jesus_mi_amigo');
define('DB_USER',     'root');
define('DB_PASS',     'root');
define('JWT_SECRET',  '08725e547914eb52b7abba9bdfe7be4a2a5e6b8d9ca977d84a20816ff4f5023c');
define('JWT_ISSUER',  'http://localhost:8000');

define('BASE_URL', '');

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
    'product_create'   => '/admin/products/create',  // GET
    'product_store'    => '/admin/products',         // POST
    'product_edit'     => '/admin/products/edit',    // GET  ?id=
    'product_update'   => '/admin/products/update',  // POST
    'product_delete'   => '/admin/products/delete',  // POST
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
];
