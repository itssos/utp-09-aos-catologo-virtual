<?php

namespace App\controllers;
use App\config\Database;
use App\models\Product;

class ProductController {
  private $db;
  public function __construct() {
    $this->db = (new Database())->getConnection();
  }

  public function list(): void {
    $prod = new Product($this->db);
    $items = $prod->readAll();   // devuelve array asociativo
    include __DIR__ . '/../views/products/list.php';
  }
}
