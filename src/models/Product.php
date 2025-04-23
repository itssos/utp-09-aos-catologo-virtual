<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use Exception;
use JsonSerializable;

class Product implements JsonSerializable
{
    private PDO $db;

    private int $product_id;
    private string $title;
    private string $description;
    private float $price;
    private int $stock_quantity;
    private int $category_id;
    private ?string $isbn;
    private ?string $publication_date;
    private string $created_at;
    private string $updated_at;

    public function __construct(PDO $db, array $data = [])
    {
        $this->db = $db;
        if ($data) {
            $this->hydrate($data);
        }
    }

    private function hydrate(array $d): void
    {
        $this->product_id       = (int)($d['product_id'] ?? 0);
        $this->title            = (string)$d['title'];
        $this->description      = (string)$d['description'];
        $this->price            = (float)$d['price'];
        $this->stock_quantity   = (int)$d['stock_quantity'];
        $this->category_id      = (int)$d['category_id'];
        $this->isbn             = $d['isbn'] ?? null;
        $this->publication_date = $d['publication_date'] ?? null;
        $this->created_at       = $d['created_at'];
        $this->updated_at       = $d['updated_at'];
    }

    public function jsonSerialize(): array
    {
        return [
            'product_id'       => $this->product_id,
            'title'            => $this->title,
            'description'      => $this->description,
            'price'            => $this->price,
            'stock_quantity'   => $this->stock_quantity,
            'category_id'      => $this->category_id,
            'images'           => [],
            'isbn'             => $this->isbn,
            'publication_date' => $this->publication_date,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }

    public function getId(): int
    {
        return $this->product_id;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM products");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => new self($this->db, $r), $rows);
    }

    public function getById(int $id): self
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE product_id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) throw new Exception('Producto no encontrado');
        return new self($this->db, $row);
    }

    public function create(array $data): int
    {
        if (empty($data['title']) || !isset($data['price'])) {
            throw new Exception('Título y precio son obligatorios');
        }
        $stmt = $this->db->prepare(
            "INSERT INTO products (title, description, price, stock_quantity, category_id, isbn, publication_date)
             VALUES (:title, :description, :price, :qty, :cat, :isbn, :pub)"
        );
        $stmt->execute([
            'title'       => $data['title'],
            'description' => $data['description'] ?? '',
            'price'       => $data['price'],
            'qty'         => $data['stock_quantity'] ?? 0,
            'cat'         => $data['category_id'],
            'isbn'        => $data['isbn'] ?? null,
            'pub'         => $data['publication_date'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];
        foreach (['title','description','price','stock_quantity','isbn','publication_date'] as $f) {
            if (isset($data[$f])) {
                $fields[] = "$f = :$f";
                $params[$f] = $data[$f];
            }
        }
        if (empty($fields)) throw new Exception("No hay datos para actualizar ".json_encode($data));
        $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE product_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE product_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}