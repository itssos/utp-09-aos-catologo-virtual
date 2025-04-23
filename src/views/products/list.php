<?php include VIEWS_PATH . 'layouts/header.php'; ?>

<div class="products-grid">
  <?php foreach ($products as $p): ?>
    <div class="product-card">
      <div class="card-image">
        <?php if (!empty($p->images)): ?>
          <img src="<?= htmlspecialchars($p->images[0]->url) ?>" alt="<?= htmlspecialchars($p->images[0]->alt_text ?: $p->title) ?>">
        <?php else: ?>
          <img src="<?= NO_IMAGE ?>" alt="Sin imagen" class="thumb">
        <?php endif; ?>
      </div>
      <div class="card-body">
        <h3 class="card-title"><?= htmlspecialchars($p->title) ?></h3>
        <p class="card-price">$<?= number_format($p->price, 2) ?></p>
        <a href="product.php?id=<?= $p->id ?>" class="card-btn">Ver detalles</a>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<style>
  /* Contenedor responsive */
  .products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 1.5rem;
    padding: 1rem;
  }

  /* Tarjeta individual */
  .product-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
  }

  /* Imagen de la tarjeta */
  .card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  /* Cuerpo de la tarjeta */
  .card-body {
    padding: 1rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .card-title {
    font-size: 1.1rem;
    margin: 0 0 0.5rem;
    color: #333;
  }

  .card-price {
    font-weight: bold;
    color: #e63946;
    margin: 0 0 1rem;
  }

  /* Botón */
  .card-btn {
    text-align: center;
    padding: 0.6rem 1rem;
    background: #457b9d;
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.2s;
  }

  .card-btn:hover {
    background: #1d3557;
  }

  /* Ajustes para móviles si quieres más separación */
  @media (max-width: 600px) {
    .products-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<?php include VIEWS_PATH . 'layouts/footer.php'; ?>