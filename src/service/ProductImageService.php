<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\ProductImage;
use Exception;

class ProductImageService
{
    private ProductImage $model;
    private string $uploadDir;
    private string $uploadUrl;

    public function __construct(ProductImage $model, string $uploadDir, string $uploadUrl)
    {
        $this->model     = $model;
        $this->uploadDir = rtrim($uploadDir, '/');
        $this->uploadUrl = rtrim($uploadUrl, '/');
    }

    public function getByProduct(int $productId): array
    {
        return $this->model->getByProduct($productId);
    }

    public function uploadImages(int $productId, array $files): void
    {
        if (empty($files['name'])) {
            return;
        }
        foreach ($files['name'] as $i => $name) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }
            $tmp = $files['tmp_name'][$i];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $filename = uniqid('prod_') . '.' . $ext;
            $dest = $this->uploadDir . '/' . $filename;
            if (!move_uploaded_file($tmp, $dest)) {
                throw new Exception("Error subiendo archivo: {$name}");
            }
            $url = $this->uploadUrl . '/' . $filename;
            $this->model->create($productId, $url, null);
        }
    }

    public function deleteImages(array $imageIds): void
    {
        foreach ($imageIds as $id) {
            $imgs = $this->model->getByProduct($id);
            // Buscar la imagen con ese ID
            foreach ($imgs as $img) {
                if ($img->jsonSerialize()['image_id'] === $id) {
                    $path = $this->uploadDir . '/' . basename($img->jsonSerialize()['url']);
                    if (is_file($path)) {
                        @unlink($path);
                    }
                    $this->model->delete($id);
                    break;
                }
            }
        }
    }
}
