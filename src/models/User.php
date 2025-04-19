<?php

namespace App\models;

use PDO;

class User {
    private PDO $conn;
    private string $table = 'users';

    public int    $user_id;
    public string $username;
    public string $email;
    public string $password_hash;

    public function __construct(PDO $db) {
        $this->conn = $db;
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
}
