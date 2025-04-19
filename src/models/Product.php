<?php
namespace App\models;
use PDO;

class Product {
  private PDO $conn;
  private string $table = 'products';
  public function __construct(PDO $db) { $this->conn = $db; }

  public function readAll(): array {
    $sql = "SELECT product_id, title, price, stock_quantity FROM {$this->table}";
    $stmt = $this->conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
