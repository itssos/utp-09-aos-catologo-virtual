<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
  .product-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    background: var(--secondary-color);
    margin-top: 20px;
  }

  .product-table thead {
    background: var(--primary-color);
    color: var(--secondary-color);
  }

  .product-table th,
  .product-table td {
    padding: 12px 15px;
    text-align: left;
  }

  .product-table tbody tr:nth-child(even) {
    background: var(--bg);
  }

  .product-table tbody tr:hover {
    background: var(--accent-color);
    color: var(--secondary-color);
  }

  /* Alertas */
  .alert {
    padding: 12px 20px;
    margin-bottom: 20px;
    border-radius: 6px;
    font-weight: 500;
  }

  .alert-success {
    background: #d4edda;
    color: #155724;
    border-left: 5px solid #28a745;
  }

  .alert-error {
    background: #f8d7da;
    color: #721c24;
    border-left: 5px solid #dc3545;
  }

  /* Botones */
  .btn-new,
  .btn-edit,
  .btn-delete {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border: none;
    border-radius: 6px;
    font-family: var(--font-family);
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  .btn-new {
    background: var(--accent-color);
    color: var(--secondary-color);
  }

  .btn-new:hover {
    background: #be845c;
  }

  .btn-edit {
    background: #3498db;
    color: #fff;
  }

  .btn-edit:hover {
    background: #2980b9;
  }

  .btn-delete {
    background: #e74c3c;
    color: #fff;
  }

  .btn-delete:hover {
    background: #c0392b;
  }

  .product-table td img.thumb {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 8px;
    vertical-align: middle;
  }
</style>

<?php if ($m = getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($m) ?></div>
<?php endif; ?>
<?php if ($m = getFlash('error')): ?>
  <div class="alert alert-error"><?= htmlspecialchars($m) ?></div>
<?php endif; ?>

<h1>Productos</h1>

<?php if (can('create_product', $pdo)): ?>
  <p>
    <a href="<?= route('product_create') ?>" class="btn-new">
      <i class="fa fa-plus"></i> Nuevo Producto
    </a>
  </p>
<?php endif; ?>

<table class="product-table">
  <thead>
    <tr>
      <th>Imagen</th>
      <th>ID</th>
      <th>Título</th>
      <th>Categoría</th>
      <th>Precio</th>
      <th>Stock</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($products as $p): ?>
      <tr>
        <td>
          <?php if (!empty($p->images)): ?>
            <img src="<?= htmlspecialchars($p->images[0]->url) ?>"
              alt="<?= htmlspecialchars($p->images[0]->alt_text ?: $p->title) ?>"
              class="thumb">
          <?php else: ?>
            <i class="fa fa-image fa-2x" style="color:#ccc"></i>
          <?php endif; ?>
        </td>
        <td><?= $p->product_id ?></td>
        <td><?= htmlspecialchars($p->title) ?></td>
        <td><?= htmlspecialchars($p->category_name) ?></td>
        <td><?= number_format($p->price, 2, ',', '.') ?></td>
        <td><?= $p->stock_quantity ?></td>
        <td>
          <?php if (can('edit_product', $pdo)): ?>
            <a href="<?= route('product_edit') ?>/<?= $p->product_id ?>"
              class="btn-edit">
              <i class="fa fa-edit"></i> Editar
            </a>
          <?php endif; ?>

          <?php if (can('delete_product', $pdo)): ?>
            <button
              class="btn-delete"
              data-id="<?= $p->product_id ?>"
              onclick="onDeleteProduct(<?= $p->product_id ?>, '<?= addslashes($p->title) ?>')">
              <i class="fa fa-trash"></i> Eliminar
            </button>
          <?php endif; ?>
          <script>
            async function onDeleteProduct(id, title) {
              if (!confirm(`¿Eliminar «${title}»?`)) return;

              try {
                const res = await fetch(`<?= API_BASE_URL ?>/api/products/${id}`, {
                  method: 'DELETE',
                  credentials: 'include',
                  headers: {
                    'Accept': 'application/json'
                  }
                });
                if (!res.ok) {
                  const text = await res.text();
                  throw new Error(text || res.statusText);
                }
                const json = await res.json();
                if (json.status !== 'success') {
                  throw new Error(json.message || 'Error al eliminar');
                }
                // Recarga la página o elimina la fila del DOM:
                window.location.reload();
              } catch (err) {
                console.error(err);
                alert('Error eliminando producto:\n' + err.message);
              }
            }
          </script>

        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include __DIR__ . '/../layouts/footer.php'; ?>