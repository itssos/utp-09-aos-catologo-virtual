<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\Category;
use Exception;

class CategoryService
{
    private Category $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function getAll(): array
    {
        return $this->category->getAll();
    }

    public function getById(int $id): array
    {
        $category = $this->category->getById($id);
        if (empty($category)) {
            throw new Exception('Categoría no encontrada');
        }
        return $category;
    }

    public function create(array $data): int
    {
        if (empty($data['name'])) {
            throw new Exception('El nombre de la categoría es obligatorio');
        }

        return $this->category->create($data);
    }

    public function update(int $id, array $data): void
    {
        if (empty($data['name'])) {
            throw new Exception('El nombre de la categoría es obligatorio');
        }

        $updated = $this->category->update($id, $data);
        if (!$updated) {
            throw new Exception('No se actualizó la categoría');
        }
    }

    public function delete(int $id): void
    {
        $deleted = $this->category->delete($id);
        if (!$deleted) {
            throw new Exception('No se pudo eliminar la categoría');
        }
    }

    public function getByParentId(int $parentId): array
    {
        return $this->category->getByParentId($parentId);
    }

    public function getFullHierarchy(int $parentId = 0): array
    {
        return $this->category->getFullHierarchy($parentId);
    }

    public function search(string $term): array
    {
        return $this->category->search($term);
    }
}
