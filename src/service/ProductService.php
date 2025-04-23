<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\Product;
use Exception;

class ProductService
{
    private Product $model;
    private ProductImageService $imgService;
    private CategoryService $catService;

    public function __construct(Product $model, ProductImageService $imgService, CategoryService $catService)
    {
        $this->model      = $model;
        $this->imgService = $imgService;
        $this->catService = $catService;
    }

    public function create(array $data, array $files): int
    {
        $id = $this->model->create($data);
        $this->imgService->uploadImages($id, $files);
        return $id;
    }

    public function update(int $id, array $data, array $files, array $deleteIds): void
    {
        $this->model->update($id, $data);
        if (!empty($deleteIds)) {
            $this->imgService->deleteImages($deleteIds);
        }
        $this->imgService->uploadImages($id, $files);
    }

    public function delete(int $id): void
    {
        $imgs = $this->model->getById($id);
        // Suponiendo que el modelo Product no carga imágenes, se podrían obtener con otro modelo
        // Aquí podríamos delegar a imgService>deleteImages([...]) si tenemos lista
        $this->model->delete($id);
    }

    public function getAll(): array
    {
        $products = $this->model->getAll();

        return array_map(function ($product) {
            $item = is_object($product) ? $product->jsonSerialize() : $product;
            return $this->enrichProduct($item);
        }, $products);
    }

    public function getById(int $id): array
    {
        $product = $this->model->getById($id);
        $item = is_object($product) ? $product->jsonSerialize() : $product;

        return $this->enrichProduct($item);
    }

    private function enrichProduct(array $product): array
    {
        $cat = $this->catService->getById((int) $product['category_id']);
        $product['category_name'] = $cat['name'] ?? 'Sin categoría';

        $images = $this->imgService->getByProduct($product['product_id']);
        $product['images'] = array_map(fn($img) => $img->jsonSerialize(), $images);

        return $product;
    }
}
