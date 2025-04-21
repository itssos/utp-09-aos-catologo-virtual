<?php

namespace App\controllers;

use App\Models\Product;
use App\Config\Database;
use App\Models\Category;
use App\Models\ProductImage;
use PDO;

class ProductController
{
  private PDO $conn;
  public function __construct()
  {
    $this->conn = Database::getConnection();
  }


  public function list(): void
  {
    $prod = new Product();
    $products = $prod->readAll();   // devuelve array asociativo
    include __DIR__ . '/../views/products/list.php';
  }

  public function adminList(): void
  {
    $prod = new Product();
    $cat  = new Category();
    $imgM = new ProductImage();
    $products = $prod->readAll();
    foreach ($products as &$product) {
      $category        = $cat->findById((int)$product->category_id);
      $product->category = $category->name;
      $product->images = $imgM->findByProductId($product->product_id);
    }

    include __DIR__ . '/../views/products/adminList.php';
  }

  public function create(): void
  {
    $categories = (new Category())->readAll();
    include __DIR__ . '/../views/products/create.php';
  }

  public function store(): void
  {
    $p = new Product();
    $product_id = $p->create($_POST);
    if ($product_id) {

      if (!empty($_POST['delete_images']) && is_array($_POST['delete_images'])) {
        $stmt = $this->conn->prepare("
      SELECT url FROM product_images
      WHERE image_id = :imgid
    ");
        $delStmt = $this->conn->prepare("
      DELETE FROM product_images
      WHERE image_id = :imgid
    ");
        foreach ($_POST['delete_images'] as $imgId) {
          // 1.a) Obtener ruta para unlink()
          $stmt->execute([':imgid' => $imgId]);
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          if ($row) {
            $filePath = __DIR__ . '/../../public' . $row['url'];
            if (is_file($filePath)) {
              unlink($filePath);
            }
            // 1.b) Borrar registro en BD
            $delStmt->execute([':imgid' => $imgId]);
          }
        }
      }


      // 2. Procesar imágenes
      $uploadDir = __DIR__ . '/../../public/uploads/products/';
      if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
      }

      if (!empty($_FILES['images']) && count($_FILES['images']['name']) > 0) {
        foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {
          if (is_uploaded_file($tmpName)) {
            // Generar nombre único
            $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
            $filename = uniqid('prod_' . $product_id . '_') . '.' . $ext;
            $destPath = $uploadDir . $filename;

            if (move_uploaded_file($tmpName, $destPath)) {
              // Insertar ruta en la BD (suponiendo PDO)
              $url = '/uploads/products/' . $filename; // ruta pública
              $stmt = $this->conn->prepare("
          INSERT INTO product_images (product_id, url, alt_text)
          VALUES (:pid, :url, :alt)
        ");
              $stmt->execute([
                ':pid' => $product_id,
                ':url' => $url,
                ':alt' => ''
              ]);
            }
          }
        }
      }

      setFlash('success', 'Producto creado.');
      header('Location: ' . route('product_store'));
    } else {
      setFlash('error', 'Error al crear.');
      header('Location: ' . route('product_create'));
    }
    exit;
  }

  public function edit(): void
  {
    $id         = (int)($_GET['id'] ?? 0);
    $product    = (new Product())->find($id);
    $categories = (new Category())->readAll();
    $imgM = new ProductImage();
    $product->images = $imgM->findByProductId($product->product_id);
    include __DIR__ . '/../views/products/edit.php';
  }

  public function update(): void
  {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $p  = new Product();
    if ($p->update($product_id, $_POST)) {


      // ——— 1) Borrar imágenes marcadas ———
      if (!empty($_POST['delete_images']) && is_array($_POST['delete_images'])) {
        $stmt = $this->conn->prepare("
      SELECT url FROM product_images
      WHERE image_id = :imgid
    ");
        $delStmt = $this->conn->prepare("
      DELETE FROM product_images
      WHERE image_id = :imgid
    ");
        foreach ($_POST['delete_images'] as $imgId) {
          // 1.a) Obtener ruta para unlink()
          $stmt->execute([':imgid' => $imgId]);
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          if ($row) {
            $filePath = __DIR__ . '/../../public' . $row['url'];
            if (is_file($filePath)) {
              unlink($filePath);
            }
            // 1.b) Borrar registro en BD
            $delStmt->execute([':imgid' => $imgId]);
          }
        }
      }

      // 2. Procesar imágenes
      $uploadDir = __DIR__ . '/../../public/uploads/products/';
      if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
      }

      if (!empty($_FILES['images']) && count($_FILES['images']['name']) > 0) {
        foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {
          if (is_uploaded_file($tmpName)) {
            // Generar nombre único
            $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
            $filename = uniqid('prod_' . $product_id . '_') . '.' . $ext;
            $destPath = $uploadDir . $filename;

            if (move_uploaded_file($tmpName, $destPath)) {
              // Insertar ruta en la BD (suponiendo PDO)
              $url = '/uploads/products/' . $filename; // ruta pública
              $stmt = $this->conn->prepare("
          INSERT INTO product_images (product_id, url, alt_text)
          VALUES (:pid, :url, :alt)
        ");
              $stmt->execute([
                ':pid' => $product_id,
                ':url' => $url,
                ':alt' => ''
              ]);
            }
          }
        }
      }


      setFlash('success', 'Producto actualizado.');
    } else {
      setFlash('error', 'Error al actualizar.');
    }
    header('Location: ' . route('product_store'));
    exit;
  }

  public function delete(): void
  {
    $id = (int)($_POST['product_id'] ?? 0);
    if ((new Product())->delete($id)) {
      setFlash('success', 'Producto eliminado.');
    } else {
      setFlash('error', 'No se pudo eliminar.');
    }
    header('Location: ' . route('product_store'));
    exit;
  }
}
