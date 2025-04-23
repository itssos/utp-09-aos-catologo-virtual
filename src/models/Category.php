<?php

declare(strict_types=1);

namespace App\Models;

use Exception;
use PDO;

class Category
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE category_id = :id");
        $stmt->execute(['id' => $id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        return $category ?: null;
    }

    public function getByParentId(?int $parentId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE parent_id = :parent_id");
        $stmt->execute(['parent_id' => $parentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO categories (name, description, parent_id) VALUES (:name, :description, :parent_id)");
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'parent_id' => $data['parent_id'] ?? null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE categories SET name = :name, description = :description, parent_id = :parent_id, updated_at = NOW() WHERE category_id = :id");
        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
            'id' => $id
        ]);
    }

    public function delete(int $id): bool
    {
        if ($this->hasChildren($id)) {
            throw new Exception("No se puede eliminar una categoría con subcategorías.");
        }

        $stmt = $this->db->prepare("DELETE FROM categories WHERE category_id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function hasChildren(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM categories WHERE parent_id = :id");
        $stmt->execute(['id' => $id]);
        return (bool)$stmt->fetchColumn();
    }

    public function getFullHierarchy(?int $parentId = null): array
    {
        $categories = $this->getByParentId($parentId);
        foreach ($categories as &$category) {
            $category['children'] = $this->getFullHierarchy((int)$category['category_id']);
        }
        return $categories;
    }

    public function search(string $term): array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE name LIKE :term OR description LIKE :term");
        $stmt->execute(['term' => "%$term%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
