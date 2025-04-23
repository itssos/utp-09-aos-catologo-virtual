<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use Exception;
use JsonSerializable;

class User implements JsonSerializable
{
    private PDO $db;

    // Atributos de usuario (columnas)
    private int $user_id;
    private string $username;
    private string $password_hash;
    private string $email;
    private ?string $full_name;
    private string $created_at;
    private string $updated_at;

    public function __construct(PDO $db, array $data = [])
    {
        $this->db = $db;
        if ($data) {
            $this->hydrate($data);
        }
    }

    private function hydrate(array $data): void
    {
        $this->user_id       = (int)($data['user_id'] ?? 0);
        $this->username      = (string)$data['username'];
        $this->password_hash = (string)$data['password_hash'];
        $this->email         = (string)$data['email'];
        $this->full_name     = $data['full_name'] ?? null;
        $this->created_at    = $data['created_at'];
        $this->updated_at    = $data['updated_at'];
    }

    public function jsonSerialize(): array
    {
        return [
            'user_id'     => $this->user_id,
            'username'    => $this->username,
            'email'       => $this->email,
            'full_name'   => $this->full_name,
            'permissions' => $this->getPermissions(),
        ];
    }

    public function getId(): int
    {
        return $this->user_id;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM users");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => new self($this->db, $row), $rows);
    }

    public function getById(int $id): self
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new Exception('Usuario no encontrado');
        }
        return new self($this->db, $row);
    }

    public function findByUsername(string $username): self
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new Exception('Usuario no encontrado');
        }
        return new self($this->db, $row);
    }

    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    public function create(array $data): int
    {
        if (empty($data['username']) || empty($data['password']) || empty($data['email'])) {
            throw new Exception('username, password y email son obligatorios');
        }
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, password_hash, email, full_name) VALUES (:username, :hash, :email, :full_name)"
        );
        $stmt->execute([
            'username'  => $data['username'],
            'hash'      => $hash,
            'email'     => $data['email'],
            'full_name' => $data['full_name'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];
        if (isset($data['username'])) {
            $fields[] = 'username = :username';
            $params['username'] = $data['username'];
        }
        if (!empty($data['password'])) {
            $fields[] = 'password_hash = :hash';
            $params['hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (isset($data['email'])) {
            $fields[] = 'email = :email';
            $params['email'] = $data['email'];
        }
        if (array_key_exists('full_name', $data)) {
            $fields[] = 'full_name = :full_name';
            $params['full_name'] = $data['full_name'];
        }
        if (empty($fields)) {
            throw new Exception('No hay datos para actualizar');
        }
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE user_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function getPermissions(): array
    {
        // Retorna lista de keys (name)
        $stmt = $this->db->prepare(
            "SELECT p.name
             FROM permissions p
             JOIN user_permissions up ON p.permission_id = up.permission_id
             WHERE up.user_id = :id"
        );
        $stmt->execute(['id' => $this->user_id]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'name');
    }

    public function hasPermission(string $permKey): bool
    {
        return in_array($permKey, $this->getPermissions(), true);
    }
}
