<?php
// ─── CONFIGURACIÓN E INICIALIZACIÓN ──────────────────
date_default_timezone_set("America/Lima");

$cliente   = "";
$documento = "";
$tipo_client = "Regular";
$metodo_pago = "Efectivo";
$productos = [];
$procesado = false;
$error_dni = "";

// ─── CAPTURA Y PROCESAMIENTO DEL FORMULARIO ──────────
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['procesar'])) {
    $cliente     = htmlspecialchars($_POST['cliente']);
    $documento   = trim($_POST['documento']);
    $tipo_client = $_POST['tipo_client'];
    $metodo_pago = $_POST['metodo_pago'];

    // 1. Validación de DNI
    if (strlen($documento) !== 8 || !ctype_digit($documento)) {
        $error_dni = "Error: El DNI debe tener exactamente 8 dígitos numéricos.";
    } else {
        $procesado = true;

        $total_productos = 0;
        $total_neto = 0;
        $total_igv = 0;

        for ($i = 1; $i <= 4; $i++) {
            $prod_nombre   = htmlspecialchars($_POST["prod_nombre_$i"]);
            $prod_cate     = $_POST["prod_cate_$i"];
            $prod_precio   = floatval($_POST["prod_precio_$i"]);
            $prod_cantidad = intval($_POST["prod_cantidad_$i"]);
            
            $total_item = $prod_precio * $prod_cantidad;
            $total_productos += $total_item;

            // 2. Determinar tasa de IGV por categoría
            switch ($prod_cate) {
                case 'panaderia':
                case 'frutas_verduras':
                    $tasa_igv = 0.00;
                    break;
                default:
                    $tasa_igv = 0.18;
                    break;
            }

            if ($tasa_igv > 0) {
                $subtotal_neto_item = $total_item / 1.18;
                $igv_item = $total_item - $subtotal_neto_item;
            } else {
                $subtotal_neto_item = $total_item;
                $igv_item = 0.00;
            }

            $total_neto += $subtotal_neto_item;
            $total_igv += $igv_item;

            $productos[] = [
                "nombre"   => $prod_nombre,
                "categoria"=> $prod_cate,
                "precio"   => $prod_precio,
                "cantidad" => $prod_cantidad,
                "tasa_igv" => $tasa_igv,
                "total"    => $total_item
            ];
        }

        // 3. Cálculo de Descuento por Monto
        $porc_desc_monto = 0;
        if ($total_productos >= 200) {
            $porc_desc_monto = 0.15; // 15%
        } elseif ($total_productos >= 100) {
            $porc_desc_monto = 0.10; // 10%
        } elseif ($total_productos >= 30) {
            $porc_desc_monto = 0.05; // 5%
        }
        $monto_desc_monto = $total_productos * $porc_desc_monto;

        // 4. Cálculo de Descuento por Tipo de Cliente
        $porc_desc_cliente = 0;
        if ($tipo_client == "Frecuente") {
            $porc_desc_cliente = 0.02;
        } elseif ($tipo_client == "VIP") {
            $porc_desc_cliente = 0.05;
        }
        $monto_desc_cliente = $total_productos * $porc_desc_cliente;

        $total_descuentos = $monto_desc_monto + $monto_desc_cliente;
        $porc_total_aplicado = ($porc_desc_monto + $porc_desc_cliente) * 100;
        $total_a_pagar = $total_productos - $total_descuentos;

        // 5. Mensaje según método de pago
        $instruccion_pago = "";
        switch ($metodo_pago) {
            case 'Efectivo':
                $instruccion_pago = "Pago en efectivo — exacto preferido";
                break;
            case 'Yape':
            case 'Plin':
                $instruccion_pago = "Mostrar QR del comercio";
                break;
            case 'Tarjeta':
                $instruccion_pago = "Insertar tarjeta en POS";
                break;
        }
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
        
        /* FORMULARIO MEJORADO CON FILTRADO DE ANCHO */
        .card-form { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 30px; border-top: 4px solid #0f3e7a; width: 100%; }
        .card-form h3 { color: #0f3e7a; font-size: 16px; text-transform: uppercase; margin-bottom: 15px; letter-spacing: 0.5px; border-bottom: 2px solid #f1f5f9; padding-bottom: 5px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 12px; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 10px; }
        .form-group label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 5px; }
        .form-group input, .form-group select { padding: 9px 12px; font-size: 13.5px; border: 1px solid #cbd5e1; border-radius: 5px; outline: none; width: 100%; }
        
        /* CONTROLES DEL CARRITO - SOLUCIÓN AL DESBORDE DEL GRID */
        .grid-products { display: grid; grid-template-columns: 2.2fr 1.6fr 1fr 1fr; gap: 10px; margin-bottom: 8px; align-items: center; width: 100%; }
        .grid-products input, .grid-products select { padding: 8px; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 4px; width: 100%; min-width: 0; box-sizing: border-box; }
        .header-grid { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px; }

        .btn-calc { width: 100%; background: #0f3e7a; color: white; border: none; padding: 14px; font-size: 14px; font-weight: 700; border-radius: 5px; cursor: pointer; text-transform: uppercase; margin-top: 10px; }
        .btn-calc:hover { background: #0a2952; }
        .error-msg { background: #fef2f2; color: #b91c1c; padding: 12px; border-radius: 6px; border-left: 4px solid #b91c1c; margin-bottom: 15px; font-size: 13.5px; font-weight: 600; }

        /* COMPROBANTE - REPLICA EXACTA ANTI-DESBORDE */
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
        
        /* AJUSTES FIJOS EN LA TABLA DE PRODUCTOS */
        .table-prod { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .table-prod th { text-align: left; font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; padding-bottom: 10px; border-bottom: 1px solid #cbd5e1; }
        .table-prod td { padding: 12px 0; font-size: 13.5px; color: #334155; vertical-align: top; border-bottom: 1px solid #f1f5f9; word-wrap: break-word; overflow: hidden; }
        
        .prod-title { font-weight: 600; color: #1e293b; display: block; }
        .prod-sub { font-size: 11px; color: #94a3b8; display: block; margin-top: 2px; }
        
        .badge { display: inline-block; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px; margin-top: 4px; }
        .badge-18 { background: #e0f2fe; color: #0369a1; }
        .badge-0 { background: #dcfce7; color: #15803d; }

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

    <div class="card-form">
        <h3>Simulador POS Minimarket Mass</h3>
        
        <?php if (!empty($error_dni)): ?>
            <div class="error-msg"><?php echo $error_dni; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Nombre del Cliente</label>
                    <input type="text" name="cliente" value="<?php echo $cliente ? $cliente : 'María López'; ?>" required>
                </div>
                <div class="form-group">
                    <label>DNI del Cliente</label>
                    <input type="text" name="documento" value="<?php echo $documento ? $documento : '87654321'; ?>" maxlength="8" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Tipo de Cliente</label>
                    <select name="tipo_client">
                        <option value="Regular" <?php echo $tipo_client == 'Regular' ? 'selected' : ''; ?>>Regular</option>
                        <option value="Frecuente" <?php echo $tipo_client == 'Frecuente' ? 'selected' : ''; ?>>Frecuente (+2%)</option>
                        <option value="VIP" <?php echo $tipo_client == 'VIP' ? 'selected' : ''; ?>>VIP (+5%)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Método de Pago</label>
                    <select name="metodo_pago">
                        <option value="Efectivo" <?php echo $metodo_pago == 'Efectivo' ? 'selected' : ''; ?>>💵 Efectivo</option>
                        <option value="Yape" <?php echo $metodo_pago == 'Yape' ? 'selected' : ''; ?>>📱 Yape</option>
                        <option value="Plin" <?php echo $metodo_pago == 'Plin' ? 'selected' : ''; ?>>📱 Plin</option>
                        <option value="Tarjeta" <?php echo $metodo_pago == 'Tarjeta' ? 'selected' : ''; ?>>💳 Tarjeta</option>
                    </select>
                </div>
            </div>

            <h4 class="header-grid" style="margin-top: 15px;">Productos en Carrito</h4>
            
            <div class="grid-products">
                <div class="header-grid">Descripción</div>
                <div class="header-grid">Categoría (IGV)</div>
                <div class="header-grid">P. Unit</div>
                <div class="header-grid">Cant.</div>
            </div>

            <div class="grid-products">
                <input type="text" name="prod_nombre_1" value="Arroz Costeño 5kg" required>
                <select name="prod_cate_1">
                    <option value="abarrotes" selected>Abarrotes (18%)</option>
                    <option value="panaderia">Panadería (0%)</option>
                </select>
                <input type="number" step="0.01" name="prod_precio_1" value="28.50" required>
                <input type="number" name="prod_cantidad_1" value="3" required>
            </div>

            <div class="grid-products">
                <input type="text" name="prod_nombre_2" value="Inca Kola 1.5L" required>
                <select name="prod_cate_2">
                    <option value="bebidas" selected>Bebidas (18%)</option>
                    <option value="panaderia">Panadería (0%)</option>
                </select>
                <input type="number" step="0.01" name="prod_precio_2" value="4.50" required>
                <input type="number" name="prod_cantidad_2" value="2" required>
            </div>

            <div class="grid-products">
                <input type="text" name="prod_nombre_3" value="Leche Gloria 1L" required>
                <select name="prod_cate_3">
                    <option value="lacteos" selected>Lácteos (18%)</option>
                    <option value="panaderia">Panadería (0%)</option>
                </select>
                <input type="number" step="0.01" name="prod_precio_3" value="5.20" required>
                <input type="number" name="prod_cantidad_3" value="4" required>
            </div>

            <div class="grid-products">
                <input type="text" name="prod_nombre_4" value="Pan de molde Bimbo" required>
                <select name="prod_cate_4">
                    <option value="abarrotes">Abarrotes (18%)</option>
                    <option value="panaderia" selected>Panadería (0%)</option>
                </select>
                <input type="number" step="0.01" name="prod_precio_4" value="6.90" required>
                <input type="number" name="prod_cantidad_4" value="1" required>
            </div>

            <button type="submit" name="procesar" class="btn-calc">Procesar Venta</button>
        </form>
    </div>

    <?php if ($procesado): ?>
    <div class="comprobante">
        
        <div class="comp-header">
            <div class="mass-logo">Mass<span>✔</span></div>
            <h2>Comprobante de Venta – Minimarket Mass</h2>
            <div class="meta-row">
                <p>Tienda: <strong>Mass Cayma</strong></p>
                <p>Periodo: <strong><?php echo date("F Y"); ?></strong></p>
            </div>
            <p style="margin-top: 4px;">Fecha de emisión: <strong><?php echo date("d/m/Y"); ?></strong> · Hora: <strong><?php echo date("H:i:s"); ?></strong></p>
        </div>

        <div class="comp-section">
            <div class="section-title">Datos del Cliente</div>
            <table class="table-data">
                <colgroup>
                    <col style="width: 40%;">
                    <col style="width: 60%;">
                </colgroup>
                <tr>
                    <td>Saludo</td>
                    <td class="val-right"><?php echo date("H") < 12 ? "Buenos días" : (date("H") < 19 ? "Buenas tardes" : "Buenas noches"); ?>, <?php echo $cliente; ?>!</td>
                </tr>
                <tr>
                    <td>DNI</td>
                    <td class="val-right"><?php echo $documento; ?></td>
                </tr>
                <tr>
                    <td>Tipo de cliente</td>
                    <td class="val-right" style="text-transform: capitalize;"><?php echo $tipo_client; ?></td>
                </tr>
            </table>
        </div>

        <div class="comp-section">
            <div class="section-title">Detalle de Productos</div>
            <table class="table-prod">
                <colgroup>
                    <col style="width: 45%;">
                    <col style="width: 20%;">
                    <col style="width: 15%;">
                    <col style="width: 20%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th style="text-align: center;">P. Unit.</th>
                        <th style="text-align: center;">Cant.</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $p): ?>
                    <tr>
                        <td>
                            <span class="prod-title"><?php echo $p['nombre']; ?></span>
                            <?php if($p['tasa_igv'] > 0): ?>
                                <span class="badge badge-18">IGV 18%</span>
                            <?php else: ?>
                                <span class="badge badge-0">IGV 0%</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center; color: #64748b;">S/ <?php echo number_format($p['precio'], 2); ?></td>
                        <td style="text-align: center; font-weight: 500;"><?php echo $p['cantidad']; ?></td>
                        <td style="text-align: right; font-weight: 600;">S/ <?php echo number_format($p['total'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>

                    <tr>
                        <td colspan="3" style="padding-top: 15px; border: none; color: #64748b;">Valor neto (sin IGV)</td>
                        <td style="padding-top: 15px; border: none; text-align: right; font-weight: 500;">S/ <?php echo number_format($total_neto, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="border: none; color: #64748b;">Total IGV</td>
                        <td style="border: none; text-align: right; font-weight: 500;">S/ <?php echo number_format($total_igv, 2); ?></td>
                    </tr>
                    <tr class="row-total-prod">
                        <td colspan="3">Total productos</td>
                        <td style="text-align: right; font-weight: 700;">S/ <?php echo number_format($total_productos, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="comp-section">
            <div class="section-title">Descuentos</div>
            <table class="table-data">
                <colgroup>
                    <col style="width: 65%;">
                    <col style="width: 35%;">
                </colgroup>
                <tr>
                    <td style="color: #64748b;">Descuento por monto (<?php echo $porc_desc_monto * 100; ?>%)</td>
                    <td class="val-right" style="color: #64748b; font-weight: 500;">- S/ <?php echo number_format($monto_desc_monto, 2); ?></td>
                </tr>
                <?php if ($monto_desc_cliente > 0): ?>
                <tr>
                    <td style="color: #64748b;">Descuento cliente <?php echo $tipo_client; ?> (<?php echo $porc_desc_cliente * 100; ?>%)</td>
                    <td class="val-right" style="color: #64748b; font-weight: 500;">- S/ <?php echo number_format($monto_desc_cliente, 2); ?></td>
                </tr>
                <?php endif; ?>
                <tr class="row-total-desc">
                    <td>Total descuentos (<?php echo $porc_total_aplicado; ?>%)</td>
                    <td style="text-align: right; font-weight: 700;">- S/ <?php echo number_format($total_descuentos, 2); ?></td>
                </tr>
            </table>
        </div>

        <div class="comp-section">
            <div class="section-title">Método de Pago</div>
            <div class="pay-box">
                <div class="pay-left">
                    <?php 
                        if($metodo_pago == 'Efectivo') echo '💵';
                        elseif($metodo_pago == 'Tarjeta') echo '💳';
                        else echo '📱';
                    ?>
                    <?php echo $metodo_pago; ?>
                </div>
                <div class="pay-right"><?php echo $instruccion_pago; ?></div>
            </div>
        </div>

        <div class="grand-total-box">
            <span class="lbl">TOTAL A PAGAR</span>
            <span class="val">S/ <?php echo number_format($total_a_pagar, 2); ?></span>
        </div>

        <div class="saving-strip">
            📝 Ahorraste en descuentos: <strong>S/ <?php echo number_format($total_descuentos, 2); ?></strong> &nbsp;·&nbsp; Descuento total aplicado: <strong><?php echo $porc_total_aplicado; ?>%</strong>
        </div>

    </div>
    
    <div class="comp-footer">
        Sistema MASS © 2026 – Generado automáticamente
    </div>
    <?php endif; ?>

</div>

</body>
</html>