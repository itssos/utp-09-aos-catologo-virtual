<?php

declare(strict_types=1);

namespace App\Api;

use App\Models\Category;
use App\Routing\Router;
use App\Service\ProductService;
use App\Service\ProductImageService;
use App\Models\Product;
use App\Models\ProductImage;
use App\Service\CategoryService;
use PDO;
use Exception;

class ApiProductController
{
    private ProductService $productService;
    private CategoryService $catService;

    public function __construct(PDO $db)
    {
        $imgModel      = new ProductImage($db);
        $cateModel     = new Category($db);
        $uploadDir     = __DIR__ . '/../../public/uploads/products';
        $uploadUrl     = '/uploads/products';
        $imgService    = new ProductImageService($imgModel, $uploadDir, $uploadUrl);
        $categoryService = new CategoryService($cateModel);

        $this->catService     = $categoryService;
        $this->productService = new ProductService(
            new Product($db),
            $imgService,
            $categoryService
        );
    }

    public function registerRoutes(Router $router): void
    {
        $router->get('/api/products', fn($p) => $this->index());
        $router->post('/api/products', fn($p) => $this->store(), 'create_product');
        $router->get('/api/products/{id}', fn($p) => $this->show((int)$p['id']), 'view_product');
        $router->put('/api/products/{id}', fn($p) => $this->update((int)$p['id']), 'edit_product');
        $router->delete('/api/products/{id}', fn($p) => $this->destroy((int)$p['id']), 'delete_product');
    }

    public function index(): void
    {
        header('Content-Type: application/json');
        try {
            $products = $this->productService->getAll();
            $data = $products;
            echo json_encode(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            $this->errorResponse($e);
        }
    }

    public function show(int $id): void
    {
        header('Content-Type: application/json');
        try {
            $prod = $this->productService->getById($id);
            echo json_encode(['status' => 'success', 'data' => $prod]);
        } catch (Exception $e) {
            http_response_code(404);
            $this->errorResponse($e);
        }
    }

    public function store(): void
    {
        header('Content-Type: application/json');
        try {
            $body = $_POST;

            if (!is_array($body)) {
                throw new Exception('Datos inválidos. Cuerpo recibido: ' . $body);
            }

            $files = $_FILES['images'] ?? [];
            $newId = $this->productService->create($body, $files);

            http_response_code(201);
            echo json_encode(['status' => 'success', 'id' => $newId]);
        } catch (Exception $e) {
            $this->errorResponse($e);
        }
    }

    public function update(int $id): void
    {
        header('Content-Type: application/json');
        try {
            $body = $_POST;
            if (!is_array($body)) {
                throw new Exception('Datos inválidos');
            }
            $files     = $_FILES['images'] ?? [];
            $deleteIds = $body['delete_images'] ?? [];
            $this->productService->update($id, $body, $files, $deleteIds);
            echo json_encode(['status' => 'success', 'message' => 'Producto actualizado']);
        } catch (Exception $e) {
            $this->errorResponse($e);
        }
    }

    public function destroy(int $id): void
    {
        header('Content-Type: application/json');
        try {
            $this->productService->delete($id);
            echo json_encode(['status' => 'success', 'message' => 'Producto eliminado']);
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
