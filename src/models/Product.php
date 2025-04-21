<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Product
{

  public int    $product_id;
  public string $title;
  public string $description;
  public float  $price;
  public int    $stock_quantity;
  public ?string $isbn;
  public ?string $publication_date;
  public int    $category_id;


  private PDO $conn;
  private string $table = 'products';
  public function __construct()
  {
    $this->conn = Database::getConnection();
  }

  public function readAll(): array
  {
    $sql = "SELECT * FROM products ORDER BY title";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();

    $stmt->setFetchMode(\PDO::FETCH_CLASS, self::class);

    return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class, [$this->conn]);
  }

  public function find(int $id): ?object
  {
    $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE product_id = :id");
    $stmt->execute(['id' => $id]);
    $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
    return $stmt->fetch() ?: null;
  }

  public function create(array $d): int|false
  {
    $sql = "INSERT INTO {$this->table}
        (title, description, price, stock_quantity, isbn, publication_date, category_id)
        VALUES (:t, :desc, :pr, :stk, :isb, :pd, :cat)";

    $stmt = $this->conn->prepare($sql);

    $success = $stmt->execute([
      't' => $d['title'],
      'desc' => $d['description'],
      'pr' => $d['price'],
      'stk' => $d['stock_quantity'],
      'isb' => $d['isbno'] ?? '',
      'pd' => $d['publication_date'],
      'cat' => $d['category_id'],
    ]);

    if ($success) {
      return (int) $this->conn->lastInsertId();
    }

    return false;
  }


  public function update(int $id, array $d): bool
  {
    $sql = "UPDATE {$this->table} SET
      title=:t, description=:desc, price=:pr,
      stock_quantity=:stk, isbn=:isbno,
      publication_date=:pd, category_id=:cat
     WHERE product_id=:id";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([
      't' => $d['title'],
      'desc' => $d['description'],
      'pr' => $d['price'],
      'stk' => $d['stock_quantity'],
      'isbno' => $d['isbnob'] ?? '',
      'pd' => $d['publication_date'],
      'cat' => $d['category_id'],
      'id' => $id,
    ]);
  }

  public function delete(int $id): bool
  {
    $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE product_id=:id");
    return $stmt->execute(['id' => $id]);
  }
}
