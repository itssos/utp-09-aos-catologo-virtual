<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use Exception;
use JsonSerializable;

class ProductImage implements JsonSerializable
{
    private PDO $db;

    private int $image_id;
    private int $product_id;
    private string $url;
    private ?string $alt_text;
    private string $created_at;

    public function __construct(PDO $db, array $data = [])
    {
        $this->db = $db;
        if ($data) {
            $this->image_id   = (int)$data['image_id'];
            $this->product_id = (int)$data['product_id'];
            $this->url        = $data['url'];
            $this->alt_text   = $data['alt_text'] ?? null;
            $this->created_at = $data['created_at'];
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'image_id'   => $this->image_id,
            'product_id' => $this->product_id,
            'url'        => $this->url,
            'alt_text'   => $this->alt_text,
            'created_at' => $this->created_at,
        ];
    }

    public function getByProduct(int $pid): array
    {
        $stmt = $this->db->prepare("SELECT * FROM product_images WHERE product_id = :pid");
        $stmt->execute(['pid' => $pid]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => new self($this->db, $r), $rows);
    }

    public function create(int $productId, string $url, ?string $alt = null): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO product_images (product_id, url, alt_text)
             VALUES (:pid, :url, :alt)"
        );
        $stmt->execute(['pid' => $productId, 'url' => $url, 'alt' => $alt]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM product_images WHERE image_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
