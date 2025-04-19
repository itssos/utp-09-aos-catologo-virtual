
<?php include __DIR__ . '/../layouts/header.php'; ?>
<h2>Catálogo</h2>
<table border="1" cellpadding="5">
  <tr><th>ID</th><th>Título</th><th>Precio</th><th>Stock</th></tr>
  <?php foreach($items as $i): ?>
  <tr>
    <td><?= $i['product_id'] ?></td>
    <td><?= htmlspecialchars($i['title']) ?></td>
    <td><?= number_format($i['price'],2) ?></td>
    <td><?= $i['stock_quantity'] ?></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
