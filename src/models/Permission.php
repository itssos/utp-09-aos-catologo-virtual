<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use Exception;

class Permission
{
    private PDO $db;

    // Atributos de permiso (columnas)
    private int $permission_id;
    private string $name;
    private ?string $description;
    private string $created_at;

    public function __construct(PDO $db, array $data = [])
    {
        $this->db = $db;
        if ($data) {
            $this->permission_id = (int)$data['permission_id'];
            $this->name          = $data['name'];
            $this->description   = $data['description'] ?? null;
            $this->created_at    = $data['created_at'];
        }
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM permissions");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM permissions WHERE permission_id = :id");
        $stmt->execute(['id' => $id]);
        $perm = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$perm) {
            throw new Exception('Permiso no encontrado');
        }
        return $perm;
    }

    public function create(array $data): int
    {
        if (empty($data['name'])) {
            throw new Exception('El nombre del permiso es obligatorio');
        }
        $stmt = $this->db->prepare(
            "INSERT INTO permissions (name, description) VALUES (:name, :description)"
        );
        $stmt->execute([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];
        if (isset($data['name'])) {
            $fields[] = 'name = :name';
            $params['name'] = $data['name'];
        }
        if (array_key_exists('description', $data)) {
            $fields[] = 'description = :description';
            $params['description'] = $data['description'];
        }
        if (empty($fields)) {
            throw new Exception('No hay datos para actualizar');
        }
        $sql = "UPDATE permissions SET " . implode(', ', $fields) . " WHERE permission_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM permissions WHERE permission_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function getByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*
             FROM permissions p
             JOIN user_permissions up ON p.permission_id = up.permission_id
             WHERE up.user_id = :user_id"
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function syncForUser(int $userId, array $permissionIds): void
    {
        $this->db->beginTransaction();
        $del = $this->db->prepare("DELETE FROM user_permissions WHERE user_id = :user_id");
        $del->execute(['user_id' => $userId]);

        $ins = $this->db->prepare("INSERT INTO user_permissions (user_id, permission_id) VALUES (:user_id, :permission_id)");
        foreach ($permissionIds as $pid) {
            $ins->execute(['user_id' => $userId, 'permission_id' => $pid]);
        }
        $this->db->commit();
    }
}
