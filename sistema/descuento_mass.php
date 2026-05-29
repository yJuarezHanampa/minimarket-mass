<?php
// ─── CONFIGURACIÓN E INICIALIZACIÓN ──────────────────
date_default_timezone_set("America/Lima");

$cliente = "";
$monto_original = 0.00;
$porcentaje_aplicado = 0;
$monto_descuento = 0.00;
$monto_final = 0.00;
$procesado = false;

// ─── CAPTURA Y PROCESAMIENTO DEL FORMULARIO ──────────
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calcular'])) {
    $cliente = htmlspecialchars($_POST['cliente']);
    $monto_original = floatval($_POST['monto_compra']);
    $procesado = true;

    // Lógica de rangos de descuento según la Sesión 2
    if ($monto_original >= 200) {
        $porcentaje_aplicado = 15; // 15% de descuento
    } elseif ($monto_original >= 100) {
        $porcentaje_aplicado = 10; // 10% de descuento
    } elseif ($monto_original >= 30) {
        $porcentaje_aplicado = 5;  // 5% de descuento
    } else {
        $porcentaje_aplicado = 0;  // Sin descuento
    }

    // Cálculos financieros
    $monto_descuento = $monto_original * ($porcentaje_aplicado / 100);
    $monto_final = $monto_original - $monto_descuento;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calculadora de Descuentos Mass</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #eef2f7; color: #1e293b; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        
        .container { width: 100%; max-width: 550px; }
        
        /* TARJETA DEL FORMULARIO */
        .card-form { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 25px; border-top: 4px solid #0f3e7a; width: 100%; }
        .card-form h3 { color: #0f3e7a; font-size: 16px; text-transform: uppercase; margin-bottom: 18px; letter-spacing: 0.5px; border-bottom: 2px solid #f1f5f9; padding-bottom: 5px; }
        
        .form-group { display: flex; flex-direction: column; margin-bottom: 15px; }
        .form-group label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; }
        .form-group input { padding: 10px 12px; font-size: 14px; border: 1px solid #cbd5e1; border-radius: 5px; outline: none; width: 100%; }
        .form-group input:focus { border-color: #0f3e7a; }
        
        .btn-calc { width: 100%; background: #0f3e7a; color: white; border: none; padding: 14px; font-size: 14px; font-weight: 700; border-radius: 5px; cursor: pointer; text-transform: uppercase; margin-top: 5px; transition: background 0.2s; }
        .btn-calc:hover { background: #0a2952; }

        /* TICKET DE COMPROBANTE ANTI-DESBORDE */
        .comprobante { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; width: 100%; }
        .comp-header { background: #0f3e7a; color: white; padding: 25px 20px; text-align: center; }
        .mass-logo { font-size: 42px; font-weight: 900; font-style: italic; letter-spacing: -2px; line-height: 1; margin-bottom: 10px; display: inline-block; }
        .mass-logo span { color: #ffcc00; }
        
        .comp-header h2 { font-size: 14px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 5px; }
        .comp-header p { color: #93c5fd; font-size: 12px; font-weight: 500; }

        .comp-section { padding: 20px 25px; border-bottom: 1px solid #f1f5f9; width: 100%; }
        .section-title { color: #1e3a8a; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; padding-bottom: 5px; border-bottom: 2px solid #0f3e7a; margin-bottom: 12px; }
        
        .table-data { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .table-data td { padding: 8px 0; font-size: 13.5px; color: #334155; word-wrap: break-word; }
        .val-right { text-align: right; font-weight: 600; color: #0f172a; }
        
        .row-discount { color: #b91c1c !important; font-weight: 600; }
        .row-discount .val-right { color: #b91c1c; }

        .badge-discount { display: inline-block; background: #fef2f2; color: #b91c1c; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px; border: 1px dashed #fca5a5; }

        /* CUADRO DE TOTAL FINAL */
        .grand-total-box { background: #0f3e7a; color: white; padding: 18px 25px; display: flex; justify-content: space-between; align-items: center; width: 100%; }
        .grand-total-box .lbl { font-size: 15px; font-weight: 800; letter-spacing: 0.5px; }
        .grand-total-box .val { font-size: 28px; font-weight: 800; color: #ffcc00; }

        .saving-strip { background: #fffbeb; border-top: 1px solid #fef3c7; padding: 12px; text-align: center; font-size: 12px; color: #b45309; font-weight: 500; width: 100%; }
        .saving-strip strong { color: #b45309; font-weight: 700; }
        
        .comp-footer { text-align: center; padding: 15px; font-size: 11px; color: #94a3b8; }
    </style>
</head>
<body>

<div class="container">

    <div class="card-form">
        <h3>Calculadora de Descuento Mass</h3>
        <form method="POST" action="">
            <div class="form-group">
                <label>Nombre del Cliente</label>
                <input type="text" name="cliente" value="<?php echo $cliente ? $cliente : 'Yenifer Juarez'; ?>" required>
            </div>
            <div class="form-group">
                <label>Monto Total de Compra (S/)</label>
                <input type="number" step="0.01" min="0" name="monto_compra" value="<?php echo $monto_original ? $monto_original : '150.00'; ?>" required>
            </div>
            <button type="submit" name="calcular" class="btn-calc">Calcular Descuento</button>
        </form>
    </div>

    <?php if ($procesado): ?>
    <div class="comprobante">
        
        <div class="comp-header">
            <div class="mass-logo">Mass<span>✔</span></div>
            <h2>Vale de Descuento Especial</h2>
            <p>Fecha: <strong><?php echo date("d/m/Y"); ?></strong> · Hora: <strong><?php echo date("H:i:s"); ?></strong></p>
        </div>

        <div class="comp-section">
            <div class="section-title">Datos del Cliente</div>
            <table class="table-data">
                <colgroup>
                    <col style="width: 35%;">
                    <col style="width: 65%;">
                </colgroup>
                <tr>
                    <td>Cliente:</td>
                    <td class="val-right"><?php echo $cliente; ?></td>
                </tr>
                <tr>
                    <td>Estado:</td>
                    <td class="val-right">
                        <?php if ($porcentaje_aplicado > 0): ?>
                            <span class="badge-discount">¡Ahorrando <?php echo $porcentaje_aplicado; ?>%!</span>
                        <?php else: ?>
                            <span style="color: #64748b; font-size: 12px;">No alcanza rango mínimo</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="comp-section">
            <div class="section-title">Desglose del Monto</div>
            <table class="table-data">
                <colgroup>
                    <col style="width: 60%;">
                    <col style="width: 40%;">
                </colgroup>
                <tr>
                    <td>Monto Original:</td>
                    <td class="val-right">S/ <?php echo number_format($monto_original, 2); ?></td>
                </tr>
                <tr>
                    <td>Porcentaje Aplicado:</td>
                    <td class="val-right"><?php echo $porcentaje_aplicado; ?>%</td>
                </tr>
                <tr class="row-discount">
                    <td>Monto del Descuento:</td>
                    <td class="val-right">- S/ <?php echo number_format($monto_descuento, 2); ?></td>
                </tr>
            </table>
        </div>

        <div class="grand-total-box">
            <span class="lbl">MONTO FINAL</span>
            <span class="val">S/ <?php echo number_format($monto_final, 2); ?></span>
        </div>

        <?php if ($monto_descuento > 0): ?>
        <div class="saving-strip">
            🎉 ¡Excelente! Lograste un ahorro neto de <strong>S/ <?php echo number_format($monto_descuento, 2); ?></strong> en tiendas Mass.
        </div>
        <?php else: ?>
        <div class="saving-strip" style="color: #64748b; background: #f8fafd;">
            💡 Compra S/ <?php echo number_format(30 - $monto_original, 2); ?> más para obtener <strong>5% de descuento</strong>.
        </div>
        <?php endif; ?>

    </div>
    
    <div class="comp-footer">
        Módulo de Descuentos Automatizado Mass © 2026
    </div>
    <?php endif; ?>

</div>

</body>
</html>