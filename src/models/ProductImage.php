<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class ProductImage
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function findByProductId(int $productId): array
    {
        $stmt = $this->conn->prepare("
      SELECT *
      FROM product_images
      WHERE product_id = :pid
      ORDER BY sort_order ASC, image_id ASC
    ");
        $stmt->execute([':pid' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
