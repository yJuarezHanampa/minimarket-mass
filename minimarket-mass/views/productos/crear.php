<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/navbar.php'; ?>

<div class="contenedor">

  <?php require __DIR__ . '/../layout/sidebar.php'; ?>

  <main>
    <div class="card">
      <h1>Registrar nuevo producto</h1>

      <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="index.php?accion=guardar-producto">
        <label>Código de barras</label>
        <input type="text" name="codigo">

        <label>Nombre</label>
        <input type="text" name="nombre">

        <label>Marca</label>
        <input type="text" name="marca">

        <label>Categoría</label>
        <select name="categoria">
          <option value="1">Abarrotes</option>
          <option value="2">Bebidas</option>
          <option value="3">Lácteos</option>
          <option value="4">Limpieza</option>
          <option value="5">Aseo Personal</option>
          <option value="6">Panadería</option>
          <option value="7">Frutas y Verduras</option>
        </select>

        <label>Precio (S/)</label>
        <input type="number" step="0.10" name="precio">

        <label>Stock</label>
        <input type="number" name="stock">

        <button type="submit">Guardar producto</button>
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