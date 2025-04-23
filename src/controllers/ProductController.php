<?php

declare(strict_types=1);

namespace App\Controllers;

use Exception;
use PDO;

class ProductController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->pdo = $pdo;
    }

    public function registerRoutes($router): void
    {
        // Frontend público
        $router->get('/', fn($p) => $this->index());
        // Panel de administración
        $router->get('/admin/productos', fn($p) => $this->adminList(), "view_product");
        $router->get('/admin/productos/create', fn($p) => $this->create(), "create_product");
        $router->post('/admin/productos', fn($p) => $this->store(), "create_product");
        $router->get('/admin/productos/edit/{id}', fn($p) => $this->edit((int)$p['id']), "view_product");
        $router->post('/admin/productos/update/{id}', fn($p) => $this->update((int)$p['id']), "edit_product");
    }

    public function index(): void
    {
        try {
            $resp = httpRequest('GET', '/api/products');
            $products = $resp->data;
        } catch (Exception $e) {
            setFlash('error', $e->getMessage());
            $products = (object) [];
        }

        include VIEWS_PATH . '/products/list.php';
    }


    public function adminList(): void
    {
        try {
            $resp = httpRequest('GET', '/api/products');
            $products = $resp->data;
        } catch (Exception $e) {
            setFlash('error', $e->getMessage());
            $products = [];
        }
        $pdo = $this->pdo;
        include VIEWS_PATH . '/products/adminList.php';
    }

    public function create(): void
    {
        try {
            $resp = httpRequest('GET', '/api/category');
            $categories = $resp->data;
        } catch (Exception $e) {
            setFlash('error', $e->getMessage());
            $categories = (object) [];
        }

        include VIEWS_PATH . '/products/create.php';
    }

    public function store(): void
    {
        try {
            // recolectar datos del formulario
            $body = $_POST;
            $resp = httpRequest('POST', '/api/products', $body);
            setFlash('success', 'Producto creado exitosamente');
        } catch (Exception $e) {
            setFlash('error', $e->getMessage());
        }
        header('Location: ' . route('product_store'));
        exit;
    }

    public function edit(int $id): void
    {
        try {
            $resp = httpRequest('GET', "/api/products/{$id}");
            $product = $resp->data;
            $resp = httpRequest('GET', '/api/category');
            $categories = $resp->data;
            include VIEWS_PATH . '/products/edit.php';
            return;
        } catch (Exception $e) {
            setFlash('error', $e->getMessage());
        }
        header('Location: ' . route('product_store'));
        exit;
    }

    public function update(int $id): void
    {
        try {
            $body = $_POST;
            $resp = httpRequest('PUT', "/api/products/{$id}", $body);
            setFlash('success', 'Producto actualizado');
        } catch (Exception $e) {
            setFlash('error', $e->getMessage());
        }
        header('Location: ' . route('product_store'));
        exit;
    }
}
