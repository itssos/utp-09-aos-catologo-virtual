<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
  /* Reutiliza el estilo del form */
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

  .product-form label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--primary-color);
    font-size: 0.95rem;
  }

  .product-form input,
  .product-form select,
  .product-form textarea {
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

  .product-form input:focus,
  .product-form select:focus,
  .product-form textarea:focus {
    outline: none;
    border-color: var(--accent-color);
  }

  .product-form textarea {
    resize: vertical;
  }

  /* Bloque de imágenes existentes */
  .image-preview.existing {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 16px;
  }

  .image-preview.existing img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 6px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
  }

  /* Preview de nuevas imágenes */
  .image-preview.new {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 16px;
  }

  .image-preview.new img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 6px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
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

  .thumbnails-container {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-top: 16px;
  }

  .thumbnail-item {
    position: relative;
    width: 150px;
    height: 150px;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
  }

  .thumbnail-item:hover {
    transform: scale(1.05);
  }

  .thumbnail-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .delete-icon {
    top: 8px;
    right: 8px;
  }

  .checkbox-wrapper {
    position: absolute;
    bottom: 8px;
    left: 8px;
    display: flex;
    align-items: center;
    justify-content: left;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 4px;
    padding: 4px 6px;
    transition: background 0.3s;
    width: 90%;
  }

  .checkbox-wrapper:hover,
  .checkbox-wrapper label:hover,
  .checkbox-wrapper input[type="checkbox"]:hover {
    background: rgba(255, 255, 255, 1);
    cursor: pointer;
  }

  .checkbox-wrapper input[type="checkbox"] {
    margin: 0;
    transform: scale(1.2);
    margin-right: 8px;
    width: 16px;
  }

  .checkbox-wrapper label {
    font-size: 0.9rem;
    color: #333;
    user-select: none;
    margin: 0;
  }
</style>

<?php if ($m = getFlash('error')): ?>
  <div class="alert alert-error"><?= htmlspecialchars($m) ?></div>
<?php endif; ?>

<form method="POST"
  action="<?= route('product_update') . "/" . $product->product_id ?>"
  class="product-form"
  enctype="multipart/form-data">

  <h1>Editar Producto</h1>
  <input type="hidden" name="product_id" value="<?= $product->product_id ?>">

  <!-- Mostrar miniaturas existentes -->
  <div class="thumbnails-container">
    <?php foreach ($product->images as $thumb): ?>
      <div class="thumbnail-item">
        <img src="<?= htmlspecialchars($thumb->url) ?>" alt="<?= htmlspecialchars($thumb->alt_text ?? '') ?>">

        <label for="thumb-<?= $thumb->image_id ?>" class="checkbox-wrapper">
          <input type="checkbox" name="delete_images[]" value="<?= $thumb->image_id ?>" id="thumb-<?= $thumb->image_id ?>">
          <i class="fa fa-trash"></i> Borrar
        </label>
      </div>
    <?php endforeach; ?>
  </div>

  <label>
    Título
    <input type="text" name="title" required
      value="<?= htmlspecialchars($product->title) ?>">
  </label>

  <label>
    Categoría
    <select name="category_id" required>
      <?php foreach ($categories as $c): ?>
        <option value="<?= $c->category_id ?>"
          <?= $c->category_id == $product->category_id ? 'selected' : '' ?>>
          <?= htmlspecialchars($c->name) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <label>
    Precio
    <input type="number" name="price" step="0.01" required
      value="<?= htmlspecialchars($product->price) ?>">
  </label>

  <label>
    Stock
    <input type="number" name="stock_quantity" required
      value="<?= htmlspecialchars($product->stock_quantity) ?>">
  </label>

  <label>
    ISBN
    <input type="text" name="isbn"
      value="<?= htmlspecialchars($product->isbn) ?>">
  </label>

  <label>
    Fecha publicación
    <input type="date" name="publication_date"
      value="<?= htmlspecialchars($product->publication_date) ?>">
  </label>

  <label>
    Descripción
    <textarea name="description"><?= htmlspecialchars($product->description) ?></textarea>
  </label>

  <!-- Input para nuevas imágenes -->
  <label>
    Agregar imágenes<br>
    <input type="file" name="images[]" accept="image/*" multiple>
  </label>

  <!-- Preview de nuevas imágenes -->
  <div class="image-preview new" id="preview"></div>

  <div class="form-actions">
    <button type="submit" class="btn-submit">
      <i class="fa fa-check"></i> Actualizar
    </button>
    <a href="<?= route('product_store') ?>" class="btn-cancel">
      <i class="fa fa-times"></i> Cancelar
    </a>
  </div>
</form>

<script>
  // Preview de las imágenes que selecciones ahora
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

  document.querySelector('.product-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const id = form.product_id.value;
    const errorContainer = document.querySelector('.alert-error');
    if (errorContainer) errorContainer.remove();

    // Recoge los campos del formulario (sin archivos)
    const payload = {
      title: form.title.value,
      category_id: parseInt(form.category_id.value, 10),
      price: parseFloat(form.price.value),
      stock_quantity: parseInt(form.stock_quantity.value, 10),
      isbn: form.isbn.value || null,
      publication_date: form.publication_date.value || null,
      description: form.description.value || '',
      // recoge IDs de imágenes marcadas para borrar
      delete_images: Array.from(form.querySelectorAll('input[name="delete_images[]"]:checked'))
        .map(cb => parseInt(cb.value, 10))
    };

    console.log(payload);
    

    try {
      const res = await fetch(`http://localhost:8001/api/products/${id}`, {
        method: 'PUT',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      if (!res.ok) {
        const text = await res.text();
        throw new Error(text || res.statusText);
      }

      const json = await res.json();
      if (json.status !== 'success') {
        throw new Error(json.message || 'Error al actualizar el producto');
      }

      // Redirige de vuelta al listado de administración
      window.location.href = '/admin/productos';
    } catch (err) {
      console.error(err);
      // Muestra el error sobre el formulario
      const div = document.createElement('div');
      div.className = 'alert alert-error';
      div.textContent = err.message;
      form.prepend(div);
    }
  });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>