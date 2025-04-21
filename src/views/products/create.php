<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
  .product-form {
    max-width: 600px;
    margin: 30px auto;
    background: var(--secondary-color);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .product-form h1 {
    font-family: var(--font-family);
    font-size: 1.8rem;
    color: var(--primary-color);
    margin-bottom: 20px;
    text-align: center;
  }

  .product-form .alert-error {
    margin-bottom: 20px;
  }

  .product-form label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--primary-color);
    font-size: 0.95rem;
  }

  .product-form label input,
  .product-form label select,
  .product-form label textarea {
    width: 100%;
    padding: 10px 12px;
    margin-top: 4px;
    margin-bottom: 16px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-family: var(--font-family);
    font-size: 0.9rem;
    transition: border-color 0.3s;
  }

  .product-form label input:focus,
  .product-form label select:focus,
  .product-form label textarea:focus {
    outline: none;
    border-color: var(--accent-color);
  }

  .product-form textarea {
    min-height: 100px;
    resize: vertical;
  }

  .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 20px;
  }

  .btn-submit,
  .btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    font-family: var(--font-family);
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  .btn-submit {
    background: var(--accent-color);
    color: var(--secondary-color);
  }

  .btn-submit:hover {
    background: #be845c;
  }

  .btn-cancel {
    background: #bdc3c7;
    color: var(--primary-color);
  }

  .btn-cancel:hover {
    background: #95a5a6;
  }

  .product-form .image-preview {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 16px;
  }

  .product-form .image-preview img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 6px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
  }
</style>

<?php if ($m = getFlash('error')): ?>
  <div class="alert alert-error"><?= htmlspecialchars($m) ?></div>
<?php endif; ?>

<form method="POST" action="<?= route('product_store') ?>" class="product-form" enctype="multipart/form-data">
  <h1>Nuevo Producto</h1>

  <label>
    Título
    <input type="text" name="title" required>
  </label>

  <label>
    Categoría
    <select name="category_id" required>
      <?php foreach ($categories as $c): ?>
        <option value="<?= $c->category_id ?>"><?= htmlspecialchars($c->name) ?></option>
      <?php endforeach; ?>
    </select>
  </label>

  <label>
    Precio
    <input type="number" name="price" step="0.01" required>
  </label>

  <label>
    Stock
    <input type="number" name="stock_quantity" required>
  </label>

  <label>
    ISBN
    <input type="text" name="isbno">
  </label>

  <label>
    Fecha de publicación
    <input type="date" name="publication_date">
  </label>

  <label>
    Descripción
    <textarea name="description"></textarea>
  </label>


  <label>
    Imágenes del producto<br>
    <input type="file" name="images[]" accept="image/*" multiple>
  </label>

  <div class="image-preview" id="preview"></div>


  <div class="form-actions">
    <button type="submit" class="btn-submit">
      <i class="fa fa-check"></i> Guardar
    </button>
    <a href="<?= route('product_store') ?>" class="btn-cancel">
      <i class="fa fa-times"></i> Cancelar
    </a>
  </div>
</form>

<script>
  // Preview de las imágenes antes de subir
  document.querySelector('input[name="images[]"]').addEventListener('change', function(e) {
    const container = document.getElementById('preview');
    container.innerHTML = '';
    Array.from(e.target.files).forEach(file => {
      const reader = new FileReader();
      reader.onload = evt => {
        const img = document.createElement('img');
        img.src = evt.target.result;
        container.appendChild(img);
      };
      reader.readAsDataURL(file);
    });
  });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>