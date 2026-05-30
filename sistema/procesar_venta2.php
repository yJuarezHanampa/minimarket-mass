<?php
declare(strict_types=1);

date_default_timezone_set("America/Lima");

require_once __DIR__ . '/../clases/Producto.php';
require_once __DIR__ . '/../clases/Cliente.php';
require_once __DIR__ . '/../clases/Venta.php';

// ─── Estado inicial ───────────────────────────────────────────
$venta     = null;
$procesado = false;
$error_dni = "";

// Valores para repoblar el formulario
$form = [
    'cliente'     => 'María López',
    'documento'   => '87654321',
    'tipo_client' => 'Regular',
    'metodo_pago' => 'Efectivo',
];

// ─── Procesamiento del formulario ─────────────────────────────
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['procesar'])) {

    $form = [
        'cliente'     => htmlspecialchars($_POST['cliente']),
        'documento'   => trim($_POST['documento']),
        'tipo_client' => $_POST['tipo_client'],
        'metodo_pago' => $_POST['metodo_pago'],
    ];

    try {
        // Cliente valida el DNI internamente y lanza excepción si falla
        $cliente = new Cliente($form['cliente'], $form['documento'], $form['tipo_client']);
        $venta   = new Venta($cliente, $form['metodo_pago']);

        for ($i = 1; $i <= 4; $i++) {
            $producto = new Producto(
                htmlspecialchars($_POST["prod_nombre_$i"]),
                $_POST["prod_cate_$i"],
                (float) $_POST["prod_precio_$i"],
                (int)   $_POST["prod_cantidad_$i"]
            );
            $venta->agregarProducto($producto);
        }

        $procesado = true;

    } catch (InvalidArgumentException $e) {
        $error_dni = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>POS Minimarket MASS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #eef2f7; color: #1e293b; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .container { width: 100%; max-width: 680px; }
        .card-form { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 30px; border-top: 4px solid #0f3e7a; width: 100%; }
        .card-form h3 { color: #0f3e7a; font-size: 16px; text-transform: uppercase; margin-bottom: 15px; letter-spacing: 0.5px; border-bottom: 2px solid #f1f5f9; padding-bottom: 5px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 12px; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 10px; }
        .form-group label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 5px; }
        .form-group input, .form-group select { padding: 9px 12px; font-size: 13.5px; border: 1px solid #cbd5e1; border-radius: 5px; outline: none; width: 100%; }
        .grid-products { display: grid; grid-template-columns: 2.2fr 1.6fr 1fr 1fr; gap: 10px; margin-bottom: 8px; align-items: center; width: 100%; }
        .grid-products input, .grid-products select { padding: 8px; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 4px; width: 100%; min-width: 0; box-sizing: border-box; }
        .header-grid { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px; }
        .btn-calc { width: 100%; background: #0f3e7a; color: white; border: none; padding: 14px; font-size: 14px; font-weight: 700; border-radius: 5px; cursor: pointer; text-transform: uppercase; margin-top: 10px; }
        .btn-calc:hover { background: #0a2952; }
        .error-msg { background: #fef2f2; color: #b91c1c; padding: 12px; border-radius: 6px; border-left: 4px solid #b91c1c; margin-bottom: 15px; font-size: 13.5px; font-weight: 600; }
        .comprobante { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; width: 100%; }
        .comp-header { background: #0f3e7a; color: white; padding: 30px; text-align: center; }
        .mass-logo { font-size: 46px; font-weight: 900; font-style: italic; letter-spacing: -2px; line-height: 1; margin-bottom: 15px; display: inline-block; }
        .comp-header h2 { font-size: 16px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px; }
        .comp-header p { color: #93c5fd; font-size: 12px; font-weight: 500; }
        .comp-header .meta-row { display: flex; justify-content: center; gap: 15px; margin-top: 4px; }
        .comp-section { padding: 22px 30px; border-bottom: 1px solid #f1f5f9; width: 100%; }
        .section-title { color: #1e3a8a; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; padding-bottom: 6px; border-bottom: 2px solid #0f3e7a; margin-bottom: 12px; }
        .table-data { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .table-data td { padding: 10px 0; font-size: 13.5px; color: #334155; word-wrap: break-word; }
        .val-right { text-align: right; font-weight: 700; color: #0f172a; }
        .table-prod { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .table-prod th { text-align: left; font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; padding-bottom: 10px; border-bottom: 1px solid #cbd5e1; }
        .table-prod td { padding: 12px 0; font-size: 13.5px; color: #334155; vertical-align: top; border-bottom: 1px solid #f1f5f9; word-wrap: break-word; overflow: hidden; }
        .prod-title { font-weight: 600; color: #1e293b; display: block; }
        .badge { display: inline-block; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px; margin-top: 4px; }
        .badge-18 { background: #e0f2fe; color: #0369a1; }
        .badge-0  { background: #dcfce7; color: #15803d; }
        .row-total-prod { background: #f0f7ff; font-weight: 600; color: #1e40af; }
        .row-total-prod td { padding: 12px 10px; border: none; }
        .row-total-desc { background: #fef2f2; font-weight: 600; color: #991b1b; }
        .row-total-desc td { padding: 12px 10px; border: none; }
        .pay-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 12px 15px; display: flex; justify-content: space-between; align-items: center; font-size: 13.5px; }
        .pay-left { font-weight: 600; color: #166534; display: flex; align-items: center; gap: 6px; }
        .pay-right { font-weight: 700; color: #166534; text-transform: uppercase; font-size: 12.5px; }
        .grand-total-box { background: #0f3e7a; color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; width: 100%; }
        .grand-total-box .lbl { font-size: 16px; font-weight: 800; letter-spacing: 0.5px; }
        .grand-total-box .val { font-size: 32px; font-weight: 800; color: #ffcc00; }
        .saving-strip { background: #fffbeb; border-top: 1px solid #fef3c7; padding: 12px; text-align: center; font-size: 12px; color: #b45309; font-weight: 500; width: 100%; }
        .saving-strip strong { color: #b45309; font-weight: 700; }
        .comp-footer { text-align: center; padding: 20px; font-size: 11px; color: #94a3b8; }
    </style>
</head>
<body>
<div class="container">

    <!-- ── FORMULARIO ─────────────────────────────────────── -->
    <div class="card-form">
        <h3>Simulador POS Minimarket Mass</h3>

        <?php if (!empty($error_dni)): ?>
            <div class="error-msg"><?= $error_dni ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Nombre del Cliente</label>
                    <input type="text" name="cliente" value="<?= $form['cliente'] ?>" required>
                </div>
                <div class="form-group">
                    <label>DNI del Cliente</label>
                    <input type="text" name="documento" value="<?= $form['documento'] ?>" maxlength="8" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tipo de Cliente</label>
                    <select name="tipo_client">
                        <?php foreach (['Regular' => 'Regular', 'Frecuente' => 'Frecuente (+2%)', 'VIP' => 'VIP (+5%)'] as $val => $label): ?>
                            <option value="<?= $val ?>" <?= $form['tipo_client'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Método de Pago</label>
                    <select name="metodo_pago">
                        <?php foreach (['Efectivo' => '💵 Efectivo', 'Yape' => '📱 Yape', 'Plin' => '📱 Plin', 'Tarjeta' => '💳 Tarjeta'] as $val => $label): ?>
                            <option value="<?= $val ?>" <?= $form['metodo_pago'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <h4 class="header-grid" style="margin-top:15px;">Productos en Carrito</h4>
            <div class="grid-products">
                <div class="header-grid">Descripción</div>
                <div class="header-grid">Categoría (IGV)</div>
                <div class="header-grid">P. Unit</div>
                <div class="header-grid">Cant.</div>
            </div>

            <?php
            $defaults = [
                1 => ['Arroz Costeño 5kg',   'abarrotes', 28.50, 3],
                2 => ['Inca Kola 1.5L',       'bebidas',    4.50, 2],
                3 => ['Leche Gloria 1L',       'lacteos',    5.20, 4],
                4 => ['Pan de molde Bimbo',    'panaderia',  6.90, 1],
            ];
            $categorias = [
                'abarrotes'      => 'Abarrotes (18%)',
                'bebidas'        => 'Bebidas (18%)',
                'lacteos'        => 'Lácteos (18%)',
                'panaderia'      => 'Panadería (0%)',
                'frutas_verduras'=> 'Frutas y Verduras (0%)',
            ];
            foreach ($defaults as $i => $d): ?>
            <div class="grid-products">
                <input type="text"   name="prod_nombre_<?= $i ?>"   value="<?= $d[0] ?>" required>
                <select name="prod_cate_<?= $i ?>">
                    <?php foreach ($categorias as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $d[1] === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" step="0.01" name="prod_precio_<?= $i ?>"   value="<?= $d[2] ?>" required>
                <input type="number"             name="prod_cantidad_<?= $i ?>" value="<?= $d[3] ?>" required>
            </div>
            <?php endforeach; ?>

            <button type="submit" name="procesar" class="btn-calc">Procesar Venta</button>
        </form>
    </div>

    <!-- ── COMPROBANTE ────────────────────────────────────── -->
    <?php if ($procesado && $venta !== null):
        $cliente = $venta->getCliente();
    ?>
    <div class="comprobante">

        <div class="comp-header">
            <div class="mass-logo">Mass<span>✔</span></div>
            <h2>Comprobante de Venta – Minimarket Mass</h2>
            <div class="meta-row">
                <p>Tienda: <strong>Mass Cayma</strong></p>
                <p>Periodo: <strong><?= date("F Y") ?></strong></p>
            </div>
            <p style="margin-top:4px;">Fecha: <strong><?= date("d/m/Y") ?></strong> · Hora: <strong><?= date("H:i:s") ?></strong></p>
        </div>

        <!-- Datos del cliente -->
        <div class="comp-section">
            <div class="section-title">Datos del Cliente</div>
            <table class="table-data">
                <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                <tr>
                    <td>Saludo</td>
                    <td class="val-right"><?= $cliente->saludo() ?></td>
                </tr>
                <tr>
                    <td>DNI</td>
                    <td class="val-right"><?= $cliente->getDni() ?></td>
                </tr>
                <tr>
                    <td>Tipo de cliente</td>
                    <td class="val-right"><?= $cliente->getTipo() ?></td>
                </tr>
            </table>
        </div>

        <!-- Detalle de productos -->
        <div class="comp-section">
            <div class="section-title">Detalle de Productos</div>
            <table class="table-prod">
                <colgroup>
                    <col style="width:45%"><col style="width:20%">
                    <col style="width:15%"><col style="width:20%">
                </colgroup>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th style="text-align:center">P. Unit.</th>
                        <th style="text-align:center">Cant.</th>
                        <th style="text-align:right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($venta->getProductos() as $p): ?>
                    <tr>
                        <td>
                            <span class="prod-title"><?= $p->getNombre() ?></span>
                            <span class="badge <?= $p->tieneIgv() ? 'badge-18' : 'badge-0' ?>">
                                IGV <?= $p->tieneIgv() ? '18%' : '0%' ?>
                            </span>
                        </td>
                        <td style="text-align:center;color:#64748b">S/ <?= number_format($p->getPrecio(), 2) ?></td>
                        <td style="text-align:center;font-weight:500"><?= $p->getCantidad() ?></td>
                        <td style="text-align:right;font-weight:600">S/ <?= number_format($p->calcularTotal(), 2) ?></td>
                    </tr>
                    <?php endforeach; ?>

                    <tr>
                        <td colspan="3" style="padding-top:15px;border:none;color:#64748b">Valor neto (sin IGV)</td>
                        <td style="padding-top:15px;border:none;text-align:right;font-weight:500">S/ <?= number_format($venta->calcularTotalNeto(), 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="border:none;color:#64748b">Total IGV</td>
                        <td style="border:none;text-align:right;font-weight:500">S/ <?= number_format($venta->calcularTotalIgv(), 2) ?></td>
                    </tr>
                    <tr class="row-total-prod">
                        <td colspan="3">Total productos</td>
                        <td style="text-align:right;font-weight:700">S/ <?= number_format($venta->calcularTotalProductos(), 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Descuentos -->
        <div class="comp-section">
            <div class="section-title">Descuentos</div>
            <table class="table-data">
                <colgroup><col style="width:65%"><col style="width:35%"></colgroup>
                <tr>
                    <td style="color:#64748b">Descuento por monto (<?= $venta->getPorcentajeDescuentoMonto() * 100 ?>%)</td>
                    <td class="val-right" style="color:#64748b;font-weight:500">- S/ <?= number_format($venta->calcularDescuentoMonto(), 2) ?></td>
                </tr>
                <?php if ($venta->calcularDescuentoCliente() > 0): ?>
                <tr>
                    <td style="color:#64748b">Descuento cliente <?= $cliente->getTipo() ?> (<?= $cliente->getPorcentajeDescuento() * 100 ?>%)</td>
                    <td class="val-right" style="color:#64748b;font-weight:500">- S/ <?= number_format($venta->calcularDescuentoCliente(), 2) ?></td>
                </tr>
                <?php endif; ?>
                <tr class="row-total-desc">
                    <td>Total descuentos (<?= $venta->calcularPorcentajeTotalDescuento() ?>%)</td>
                    <td style="text-align:right;font-weight:700">- S/ <?= number_format($venta->calcularTotalDescuentos(), 2) ?></td>
                </tr>
            </table>
        </div>

        <!-- Método de pago -->
        <div class="comp-section">
            <div class="section-title">Método de Pago</div>
            <div class="pay-box">
                <div class="pay-left">
                    <?php
                        $iconos = ['Efectivo' => '💵', 'Tarjeta' => '💳', 'Yape' => '📱', 'Plin' => '📱'];
                        echo $iconos[$venta->getMetodoPago()] ?? '💰';
                    ?>
                    <?= $venta->getMetodoPago() ?>
                </div>
                <div class="pay-right"><?= $venta->getInstruccionPago() ?></div>
            </div>
        </div>

        <div class="grand-total-box">
            <span class="lbl">TOTAL A PAGAR</span>
            <span class="val">S/ <?= number_format($venta->calcularTotalAPagar(), 2) ?></span>
        </div>

        <div class="saving-strip">
            📝 Ahorraste: <strong>S/ <?= number_format($venta->calcularTotalDescuentos(), 2) ?></strong>
            &nbsp;·&nbsp; Descuento total: <strong><?= $venta->calcularPorcentajeTotalDescuento() ?>%</strong>
        </div>

    </div>

    <div class="comp-footer">Sistema MASS © 2026 – Generado automáticamente</div>
    <?php endif; ?>

</div>
</body>
</html>