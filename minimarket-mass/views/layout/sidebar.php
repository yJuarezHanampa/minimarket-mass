<?php
$accion = $_GET['accion'] ?? '';
?>
<aside class="sidebar">
  <a href="index.php?accion=catalogo"
     class="<?= $accion === 'catalogo' ? 'activo' : '' ?>">
    📦 Catálogo
  </a>
  <a href="index.php?accion=nuevo-producto"
     class="<?= $accion === 'nuevo-producto' ? 'activo' : '' ?>">
    ➕ Nuevo producto
  </a>
  <a href="index.php?accion=editar-producto"
     class="<?= $accion === 'editar-producto' ? 'activo' : '' ?>">
    ✏️ Editar
  </a>
  <a href="index.php?accion=reportes"
     class="<?= $accion === 'reportes' ? 'activo' : '' ?>">
    📊 Reportes
  </a>
</aside>