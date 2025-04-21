<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class User {
    private PDO $conn;
    private string $table = 'users';

    public int    $user_id;
    public string $username;
    public string $email;
    public string $password_hash;
    public ?string $full_name;

    /** @var string[] */
    private ?array $permissions = null;


    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /** Verifica si ya existe username o email */
    public function exists(string $username, string $email): bool {
        $sql = "SELECT 1 FROM {$this->table} WHERE username = :u OR email = :e LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':u'=>$username, ':e'=>$email]);
        return (bool) $stmt->fetchColumn();
    }

    /** Hash de contraseña */
    public function setPassword(string $password): void {
        $this->password_hash = password_hash($password, PASSWORD_BCRYPT);
    }

    /** Inserta un nuevo usuario */
    public function create(): bool {
        $sql  = "INSERT INTO {$this->table} (username, email, password_hash)
                 VALUES (:u, :e, :p)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':u'=>$this->username,
            ':e'=>$this->email,
            ':p'=>$this->password_hash
        ]);
    }

    /** Carga usuario por username */
    public function readByUsername(string $username): bool {
        $sql  = "SELECT * FROM {$this->table} WHERE username = :u LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':u'=>$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->user_id       = (int)$row['user_id'];
            $this->username      = $row['username'];
            $this->email         = $row['email'];
            $this->password_hash = $row['password_hash'];
            return true;
        }
        return false;
    }

    /**
     * Busca un usuario por su ID.
     */
    public static function findById(int $id): ?self {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            SELECT user_id, username, email, full_name
            FROM users
            WHERE user_id = ?
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        $u = new self();
        $u->user_id   = (int)$row['user_id'];
        $u->username  = $row['username'];
        $u->email     = $row['email'];
        $u->full_name = $row['full_name'];
        return $u;
    }

    /**
     * Carga los permisos del usuario en memoria.
     */
    private function loadPermissions(): void {
        if ($this->permissions !== null) {
            return;
        }
        $db = Database::getConnection();
        $stmt = $db->prepare('
            SELECT p.name
            FROM permissions p
            JOIN user_permissions up ON up.permission_id = p.permission_id
            WHERE up.user_id = ?
        ');
        $stmt->execute([$this->user_id]);
        $this->permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Verifica si el usuario tiene el permiso dado.
     */
    public function hasPermission(string $permName): bool {
        $this->loadPermissions();
        return in_array($permName, $this->permissions, true);
    }

}
