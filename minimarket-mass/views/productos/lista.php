<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/navbar.php'; ?>

<div class="contenedor">

  <?php require __DIR__ . '/../layout/sidebar.php'; ?>

  <main>

    <?php if (!empty($_GET['eliminado'])): ?>
      <div id="msg-eliminado" class="msg-exito">
        ✅ Producto eliminado correctamente.
        <button onclick="this.parentElement.remove()">×</button>
      </div>
    <?php endif; ?>

    <?php if (!empty($_GET['editado'])): ?>
      <div id="msg-editado" class="msg-exito">
        ✅ Producto actualizado correctamente.
        <button onclick="this.parentElement.remove()">×</button>
      </div>
    <?php endif; ?>
    
    <h1>Catálogo del Minimarket Mass</h1>
    <p>Total de productos: <strong><?= count($productos) ?></strong></p>
    <input type="text" id="buscador" placeholder="🔍 Buscar por código o nombre..."
        style="margin: 12px 0; padding: 10px; width: 100%; max-width: 400px;
              border: 1px solid #d7dde6; border-radius: 8px; font-size: 14px;">

    <table>
      <thead>
        <tr>
          <th>Código</th>
          <th>Nombre</th>
          <th>Precio</th>
          <th>Precio con IGV</th>
          <th>Stock</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="tabla-productos">
        <?php foreach ($productos as $p): ?>
        <tr> 
          <td><?= htmlspecialchars($p->getCodigo()) ?></td>
          <td><?= htmlspecialchars($p->getNombre()) ?></td>
          <td><?= 'S/ ' . number_format($p->getPrecio(), 2) ?></td>
          <td><?= 'S/ ' . number_format($p->precioConIGV(), 2) ?></td>
          <td <?= $p->getStock() === 0 ? 'class="sin-stock"' : '' ?>>
            <?= $p->getStock() ?> unidades
          </td>
          <td>
            <a href="index.php?accion=editar-producto&codigo=<?= urlencode($p->getCodigo()) ?>">
              ✏️ Editar
            </a>
            <button class="btn-eliminar" onclick="abrirModal(
              '<?= htmlspecialchars(addslashes($p->getCodigo())) ?>',
              '<?= htmlspecialchars(addslashes($p->getNombre())) ?>',
              'S/ <?= number_format($p->getPrecio(), 2) ?>',
              '<?= $p->getStock() ?> unidades'
            )">🗑️ Eliminar</button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </main>

</div>

<!-- Modal -->
<div id="modal-eliminar" class="modal-overlay">
  <div class="modal-box">
    <div class="modal-icono">⚠️</div>
    <h2 class="modal-titulo">Eliminar producto</h2>
    <p class="modal-descripcion">
      ¿Estás seguro de que deseas eliminar este producto?<br>
      Esta acción <strong>no se puede deshacer</strong>.
    </p>
    <table class="modal-tabla">
      <tr><th>Código</th><td id="modal-codigo"></td></tr>
      <tr><th>Nombre</th><td id="modal-nombre"></td></tr>
      <tr><th>Precio</th><td id="modal-precio"></td></tr>
      <tr><th>Stock</th> <td id="modal-stock"></td></tr>
    </table>
    <div class="modal-acciones">
      <button onclick="cerrarModal()" class="btn-cancelar">Cancelar</button>
      <a id="modal-link-eliminar" href="#" class="btn-confirmar-eliminar">🗑️ Sí, eliminar</a>
    </div>
  </div>
</div>

<script>
  const modal = document.getElementById('modal-eliminar');

  function abrirModal(codigo, nombre, precio, stock) {
    document.getElementById('modal-codigo').textContent = codigo;
    document.getElementById('modal-nombre').textContent = nombre;
    document.getElementById('modal-precio').textContent = precio;
    document.getElementById('modal-stock').textContent  = stock;
    document.getElementById('modal-link-eliminar').href =
      'index.php?accion=eliminar-producto&codigo=' + encodeURIComponent(codigo);
    modal.classList.add('activo');
  }

  function cerrarModal() {
    modal.classList.remove('activo');
  }

  modal.addEventListener('click', function(e) {
    if (e.target === modal) cerrarModal();
  });

  const msg = document.getElementById('msg-eliminado');
  if (msg) setTimeout(() => msg.remove(), 4000);

  const msgEditado = document.getElementById('msg-editado');
  if (msgEditado) setTimeout(() => msgEditado.remove(), 4000);

  document.getElementById('buscador').addEventListener('input', function () {
    const texto = this.value.toLowerCase();
    const filas = document.querySelectorAll('#tabla-productos tr');

    filas.forEach(function (fila) {
      const codigo = fila.cells[0].textContent.toLowerCase();
      const nombre = fila.cells[1].textContent.toLowerCase();
      fila.style.display = (codigo.includes(texto) || nombre.includes(texto)) ? '' : 'none';
    });
  });
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
</body>
</html>