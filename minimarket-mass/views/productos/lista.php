<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/navbar.php'; ?>

<div class="contenedor">

  <?php require __DIR__ . '/../layout/sidebar.php'; ?>

  <main>
    <h1>Catálogo del Minimarket Mass</h1>
    <p>Total de productos: <strong><?= count($productos) ?></strong></p>

    <table>
      <thead>
        <tr>
          <th>Código</th>
          <th>Nombre</th>
          <th>Precio</th>
          <th>Precio con IGV</th>
          <th>Stock</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($productos as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p->getCodigo()) ?></td>
          <td><?= htmlspecialchars($p->getNombre()) ?></td>
          <td><?= 'S/ ' . number_format($p->getPrecio(), 2) ?></td>
          <td><?= 'S/ ' . number_format($p->precioConIGV(), 2) ?></td>
          <td <?= $p->getStock() === 0 ? 'class="sin-stock"' : '' ?>>
            <?= $p->getStock() ?> unidades
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </main>

</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
</body>
</html>