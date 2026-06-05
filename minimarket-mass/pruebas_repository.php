<?php
declare(strict_types=1);
require_once __DIR__ . '/models/ProductoRepository.php';

$repo = new ProductoRepository();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Comprobante de Verificación — Minimarket Mass</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --mass-blue-dark: #0F448D;
      --mass-blue-light: #1351A5;
      --mass-blue-bg: #F0F4FA;
      --mass-accent: #0081E9;
      --teal-50: #E1F5EE;
      --teal-600: #0F6E56;
      --amber-50: #FAEEDA;
      --amber-600: #854F0B;
      --red-50: #FCEBEB;
      --red-600: #E24B4A;
      --gray-50: #F8F9FA;
      --gray-100: #E5E9F0;
      --gray-400: #9AA4B2;
      --gray-600: #697586;
      --gray-800: #333E5C;
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: #F4F6F9;
      color: var(--gray-800);
      min-height: 100vh;
    }

    /* HEADER CORPORATIVO MASS */
    header {
      background: var(--mass-blue-dark);
      padding: 1.5rem 2rem;
      text-align: center;
      color: #fff;
      border-bottom: 5px solid var(--mass-accent);
    }

    .brand-logo {
      font-size: 32px;
      font-weight: 700;
      font-style: italic;
      letter-spacing: -1px;
      margin-bottom: 4px;
      display: inline-block;
    }

    header h1 {
      font-size: 16px;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-weight: 600;
      margin-bottom: 6px;
    }

    .header-meta {
      font-family: 'DM Mono', monospace;
      font-size: 12px;
      color: #A3C5F7;
    }

    /* CONTENEDOR PRINCIPAL */
    main {
      max-width: 850px;
      margin: 2rem auto;
      padding: 0 1rem;
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    /* SECCIONES ESTILO TICKET */
    section {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(51, 62, 92, 0.06);
      border: 1px solid var(--gray-100);
      overflow: hidden;
    }

    /* CONTROL DE MÉTODOS */
    .section-header {
      background: var(--mass-blue-bg);
      padding: 12px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--gray-100);
    }

    .section-title-wrapper {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .section-number {
      width: 24px;
      height: 24px;
      background: var(--mass-blue-dark);
      color: #fff;
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 700;
      font-family: 'DM Mono', monospace;
    }

    .section-title {
      font-size: 14px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--mass-blue-dark);
    }

    .section-method {
      font-family: 'DM Mono', monospace;
      font-size: 12px;
      color: var(--mass-blue-light);
      font-weight: 500;
    }

    .section-body {
      padding: 20px;
    }

    /* TARJETAS DE PRODUCTO (GRID) */
    .cards-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 12px;
    }

    .product-card {
      background: #fff;
      border: 1px solid var(--gray-100);
      border-radius: 6px;
      padding: 14px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      gap: 8px;
      min-height: 90px;
    }

    .product-card-meta {
      font-size: 11px;
      text-transform: uppercase;
      color: var(--gray-400);
      font-weight: 600;
    }

    .product-card-name {
      font-size: 14px;
      font-weight: 600;
      color: var(--gray-800);
    }

    .product-card-price {
      font-family: 'DM Mono', monospace;
      font-size: 15px;
      font-weight: 700;
      color: var(--mass-blue-dark);
    }

    /* ESTILOS DE TABLAS */
    .table-wrapper {
      border: 1px solid var(--gray-100);
      border-radius: 6px;
      overflow: hidden;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    thead th {
      background: var(--gray-50);
      padding: 12px 16px;
      text-align: left;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--gray-600);
      border-bottom: 1px solid var(--gray-100);
    }

    tbody tr {
      border-bottom: 1px solid var(--gray-50);
    }

    tbody tr:last-child { border-bottom: none; }

    td {
      padding: 12px 16px;
      color: var(--gray-800);
    }

    td.mono {
      font-family: 'DM Mono', monospace;
    }

    td.price { 
      font-weight: 600; 
      color: var(--mass-blue-dark);
    }

    /* BADGES DE REPOSICIÓN */
    .badge {
      display: inline-block;
      font-size: 11px;
      padding: 4px 10px;
      border-radius: 4px;
      font-weight: 600;
    }

    .badge-ok { background: var(--teal-50); color: var(--teal-600); }
    .badge-low { background: var(--amber-50); color: var(--amber-600); }
    .badge-critical { background: var(--red-50); color: var(--red-600); }

    /* FILA DE ESTADÍSTICAS */
    .stats-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 16px;
    }

    .stat-card {
      background: var(--gray-50);
      border: 1px dashed var(--gray-400);
      border-radius: 6px;
      padding: 16px;
    }

    .stat-label {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      color: var(--gray-600);
      margin-bottom: 6px;
      display: block;
    }

    .stat-value {
      font-size: 28px;
      font-weight: 700;
      font-family: 'DM Mono', monospace;
      color: var(--mass-blue-dark);
    }

    .empty {
      text-align: center;
      padding: 1.5rem;
      color: var(--gray-400);
      font-size: 14px;
      border: 1px dashed var(--gray-100);
      border-radius: 6px;
    }

    footer {
      text-align: center;
      padding: 2rem;
      font-size: 12px;
      color: var(--gray-600);
      font-family: 'DM Mono', monospace;
      background: var(--mass-blue-bg);
      border-top: 1px solid var(--gray-100);
      margin-top: 4rem;
    }
  </style>
</head>
<body>

<header>
  <div class="brand-logo">Mass✓</div>
  <h1>COMPROBANTE DE VERIFICACIÓN — REPOSITORIO</h1>
  <div class="header-meta">Entorno: Desarrollo PDO-MySQL | Sede: Virtual</div>
</header>

<main>

  <?php $porNombre = $repo->buscarPorNombre('Inca'); ?>
  <section>
    <div class="section-header">
      <div class="section-title-wrapper">
        <div class="section-number">1</div>
        <span class="section-title">Resultados de búsqueda</span>
      </div>
      <span class="section-method">buscarPorNombre('Inca')</span>
    </div>
    <div class="section-body">
      <?php if (empty($porNombre)): ?>
        <div class="empty">No se encontraron productos coincidentes</div>
      <?php else: ?>
        <div class="cards-grid">
          <?php foreach ($porNombre as $p): ?>
          <div class="product-card">
            <div class="product-card-name"><?= htmlspecialchars($p->getNombre()) ?></div>
            <div class="product-card-price">S/ <?= number_format($p->getPrecio(), 2) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <?php $bebidas = $repo->obtenerPorCategoria(2); ?>
  <section>
    <div class="section-header">
      <div class="section-title-wrapper">
        <div class="section-number">2</div>
        <span class="section-title">Productos por categoría — Bebidas</span>
      </div>
      <span class="section-method">obtenerPorCategoria(2)</span>
    </div>
    <div class="section-body">
      <?php if (empty($bebidas)): ?>
        <div class="empty">Categoría sin productos registrados</div>
      <?php else: ?>
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Descripción del Producto</th>
                <th>Stock Disponible</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($bebidas as $p): 
                $stock = $p->getStock();
                $cls = $stock >= 150 ? 'badge-ok' : ($stock >= 100 ? 'badge-low' : 'badge-critical');
              ?>
              <tr>
                <td><?= htmlspecialchars($p->getNombre()) ?></td>
                <td><span class="badge <?= $cls ?>"><?= $stock ?> uds.</span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <?php $bajoStock = $repo->obtenerBajoStock(100); ?>
  <section>
    <div class="section-header">
      <div class="section-title-wrapper">
        <div class="section-number">3</div>
        <span class="section-title">Reporte de alerta de reposición</span>
      </div>
      <span class="section-method">obtenerBajoStock(100)</span>
    </div>
    <div class="section-body">
      <?php if (empty($bajoStock)): ?>
        <div class="empty">Todo ok. Ningún producto por debajo del umbral crítico</div>
      <?php else: ?>
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Producto Crítico</th>
                <th>Precio Unit.</th>
                <th>Stock Actual</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($bajoStock as $p):
                $stock = $p->getStock();
                $cls = $stock < 50 ? 'badge-critical' : 'badge-low';
              ?>
              <tr>
                <td><?= htmlspecialchars($p->getNombre()) ?></td>
                <td class="mono price">S/ <?= number_format($p->getPrecio(), 2) ?></td>
                <td><span class="badge <?= $cls ?>"><?= $stock ?> uds.</span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <?php
  $total = $repo->contarTotalProductos();
  $masCaros = $repo->obtenerMasCaros(3);
  ?>
  <section>
    <div class="section-header">
      <div class="section-title-wrapper">
        <div class="section-number">4</div>
        <span class="section-title">Resumen general del catálogo</span>
      </div>
      <span class="section-method">contarTotalProductos()</span>
    </div>
    <div class="section-body">
      <div class="stats-row">
        <div class="stat-card">
          <span class="stat-label">Cantidad Total de Productos</span>
          <span class="stat-value"><?= $total ?></span>
        </div>
        <?php if (!empty($masCaros)): ?>
        <div class="stat-card">
          <span class="stat-label">Producto de Mayor Valor</span>
          <div style="font-size:15px; font-weight:700; color:var(--gray-800); margin-bottom: 2px;">
            <?= htmlspecialchars($masCaros[0]->getNombre()) ?>
          </div>
          <div style="font-family:'DM Mono',monospace; font-size:14px; font-weight:700; color:var(--mass-blue-dark);">
            S/ <?= number_format($masCaros[0]->getPrecio(), 2) ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section>
    <div class="section-header">
      <div class="section-title-wrapper">
        <div class="section-number">5</div>
        <span class="section-title">Top 3 Productos de Mayor Valor</span>
      </div>
      <span class="section-method">obtenerMasCaros(3)</span>
    </div>
    <div class="section-body">
      <?php if (empty($masCaros)): ?>
        <div class="empty">No hay datos de precios disponibles</div>
      <?php else: ?>
        <div class="cards-grid">
          <?php foreach ($masCaros as $i => $p): ?>
          <div class="product-card" style="border-top: 3px solid var(--mass-accent);">
            <div class="product-card-meta">Ranking #<?= $i+1 ?></div>
            <div class="product-card-name"><?= htmlspecialchars($p->getNombre()) ?></div>
            <div class="product-card-price">S/ <?= number_format($p->getPrecio(), 2) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <?php $gloria = $repo->buscarPorMarca('Gloria'); ?>
  <section>
    <div class="section-header">
      <div class="section-title-wrapper">
        <div class="section-number">6</div>
        <span class="section-title">Filtro por fabricante — Gloria</span>
      </div>
      <span class="section-method">buscarPorMarca('Gloria')</span>
    </div>
    <div class="section-body">
      <?php if (empty($gloria)): ?>
        <div class="empty">No se registran productos para esta marca</div>
      <?php else: ?>
        <div class="cards-grid">
          <?php foreach ($gloria as $p): ?>
          <div class="product-card">
            <div class="product-card-name"><?= htmlspecialchars($p->getNombre()) ?></div>
            <div class="product-card-price">S/ <?= number_format($p->getPrecio(), 2) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

</main>

<footer>
  MINIMARKET MASS — PRUEBAS DE INTEGRACIÓN REPOSITORY<br>
  Fecha de generación: <?= date('d/m/Y') ?> · Hora: <?= date('H:i:s') ?>
</footer>

</body>
</html>