<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/navbar.php'; ?>

<div class="contenedor">
  <?php require __DIR__ . '/../layout/sidebar.php'; ?>

  <main>
    <div class="card">
      <h1>✏️ Editar Producto</h1>

      <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="index.php?accion=actualizar-producto">

        <input type="hidden" name="codigo" value="<?= htmlspecialchars($producto->getCodigo()) ?>">

        <label>Código de barras</label>
        <input type="text" value="<?= htmlspecialchars($producto->getCodigo()) ?>" disabled>

        <label>Nombre</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($producto->getNombre()) ?>" required>

        <label>Precio (S/)</label>
        <input type="number" step="0.01" min="0" name="precio" value="<?= $producto->getPrecio() ?>" required>

        <label>Stock</label>
        <input type="number" min="0" name="stock" value="<?= $producto->getStock() ?>" required>

        <button type="submit">💾 Guardar cambios</button>
      </form>

      <p style="margin-top:14px">
        <a href="index.php?accion=catalogo">← Volver al catálogo</a>
      </p>
    </div>
  </main>

</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
</body>
</html>