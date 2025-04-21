<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Category
{
    private PDO $conn;
    private string $table = 'categories';

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function readAll(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }


    public function findById(int $id): ?object
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE category_id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ?: null;
    }
}
