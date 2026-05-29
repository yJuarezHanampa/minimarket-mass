<?php
// ─── CONFIGURACIÓN E INICIALIZACIÓN ──────────────────
date_default_timezone_set("America/Lima");

// Valores por defecto para el simulador
$cajero      = "Juan Pérez";
$dni         = "12345678";
$ventas      = 25000.00;
$antiguedad  = 3;
$tienda      = "Mass Cayma";
$procesado   = true; // Muestra el reporte por defecto
$error_dni   = "";

// ─── CAPTURA Y PROCESAMIENTO DEL FORMULARIO ──────────
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cajero     = htmlspecialchars($_POST['cajero']);
    $dni        = trim($_POST['dni']);
    $ventas     = floatval($_POST['ventas']);
    $antiguedad = intval($_POST['antiguedad']);
    $tienda     = htmlspecialchars($_POST['tienda']);

    // Validación de DNI
    if (strlen($dni) !== 8 || !ctype_digit($dni)) {
        $error_dni = "Error: El DNI del cajero debe tener exactamente 8 dígitos numéricos.";
        $procesado = false;
    } else {
        $procesado = true;
    }
}

if ($procesado) {
    // 1. Saludo según la hora actual
    $hora = date("H");
    if ($hora >= 5 && $hora <= 11) {
        $saludo = "Buenos días";
    } elseif ($hora >= 12 && $hora <= 18) {
        $saludo = "Buenas tardes";
    } elseif ($hora >= 19 && $hora <= 23) {
        $saludo = "Buenas noches";
    } else {
        $saludo = "Turno de Madrugada";
    }

    // 2. Bonificación base por ventas (if/elseif)
    $porc_bono_ventas = 0;
    if ($ventas < 10000) {
        $porc_bono_ventas = 0.00;
    } elseif ($ventas <= 20000) {
        $porc_bono_ventas = 0.03; // 3%
    } elseif ($ventas <= 35000) {
        $porc_bono_ventas = 0.05; // 5%
    } else {
        $porc_bono_ventas = 0.07; // 7%
    }
    $monto_bono_ventas = $ventas * $porc_bono_ventas;

    // 3. Bono adicional por antigüedad (switch por rangos)
    $monto_bono_antiguedad = 0;
    switch (true) {
        case ($antiguedad < 1):
            $monto_bono_antiguedad = 0;
            break;
        case ($antiguedad >= 1 && $antiguedad <= 2):
            $monto_bono_antiguedad = 50;
            break;
        case ($antiguedad >= 3 && $antiguedad <= 4):
            $monto_bono_antiguedad = 100;
            break;
        case ($antiguedad >= 5):
            $monto_bono_antiguedad = 200;
            break;
    }

    // Cálculo final
    $total_bonificaciones = $monto_bono_ventas + $monto_bono_antiguedad;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Bonificación · Minimarket Mass</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #eef2f7; color: #1e293b; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        
        .container { width: 100%; max-width: 580px; }
        
        /* FORMULARIO EDITABLE DE CONTROL */
        .card-form { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 25px; border-top: 4px solid #0f3e7a; width: 100%; }
        .card-form h3 { color: #0f3e7a; font-size: 16px; text-transform: uppercase; margin-bottom: 15px; letter-spacing: 0.5px; border-bottom: 2px solid #f1f5f9; padding-bottom: 5px; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 5px; }
        .form-group input { padding: 9px 12px; font-size: 13.5px; border: 1px solid #cbd5e1; border-radius: 5px; outline: none; width: 100%; }
        .form-group input:focus { border-color: #0f3e7a; }
        
        .btn-update { background: #0f3e7a; color: white; border: none; padding: 10px 15px; font-size: 13px; font-weight: 700; border-radius: 5px; cursor: pointer; text-transform: uppercase; grid-column: span 1; height: 38px; align-self: flex-end; }
        .btn-update:hover { background: #0a2952; }
        .error-msg { background: #fef2f2; color: #b91c1c; padding: 12px; border-radius: 6px; border-left: 4px solid #b91c1c; margin-bottom: 15px; font-size: 13.5px; font-weight: 600; }

        /* REPORTE TICKET ANTI-DESBORDE */
        .comprobante { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; width: 100%; }
        .comp-header { background: #0f3e7a; color: white; padding: 25px 20px; text-align: center; }
        .mass-logo { font-size: 42px; font-weight: 900; font-style: italic; letter-spacing: -2px; line-height: 1; margin-bottom: 10px; display: inline-block; }
        .mass-logo span { color: #ffcc00; }
        
        .comp-header h2 { font-size: 14px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 5px; }
        .comp-header p { color: #93c5fd; font-size: 12px; font-weight: 500; }

        .comp-section { padding: 20px 25px; border-bottom: 1px solid #f1f5f9; width: 100%; }
        .section-title { color: #1e3a8a; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; padding-bottom: 5px; border-bottom: 2px solid #0f3e7a; margin-bottom: 12px; }
        
        .table-data { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .table-data td { padding: 9px 0; font-size: 13.5px; color: #334155; word-wrap: break-word; }
        .val-right { text-align: right; font-weight: 600; color: #0f172a; }
        
        /* Filas de colores para desglose */
        .row-calc { color: #475569; }
        .row-calc .val-right { font-weight: 500; }
        
        .row-total-bonos { background: #f0f7ff; font-weight: 600; color: #1e40af; }
        .row-total-bonos td { padding: 12px 10px; border: none; color: #1e40af !important; }
        .row-total-bonos .val-right { color: #1e40af; font-weight: 700; }

        /* BANNER DE INCENTIVO FINAL */
        .grand-total-box { background: #0f3e7a; color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; width: 100%; }
        .grand-total-box .lbl { font-size: 15px; font-weight: 800; letter-spacing: 0.5px; }
        .grand-total-box .val { font-size: 30px; font-weight: 800; color: #ffcc00; }
        
        .saving-strip { background: #fffbeb; border-top: 1px solid #fef3c7; padding: 12px; text-align: center; font-size: 12px; color: #b45309; font-weight: 500; width: 100%; }
        .saving-strip strong { color: #b45309; font-weight: 700; }

        .comp-footer { text-align: center; padding: 15px; font-size: 11px; color: #94a3b8; }
    </style>
</head>
<body>

<div class="container">

    <div class="card-form">
        <h3>Panel de Control · Bonificación Cajeros</h3>
        
        <?php if (!empty($error_dni)): ?>
            <div class="error-msg"><?php echo $error_dni; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nombre Cajero</label>
                    <input type="text" name="cajero" value="<?php echo $cajero; ?>" required>
                </div>
                <div class="form-group">
                    <label>DNI</label>
                    <input type="text" name="dni" value="<?php echo $dni; ?>" maxlength="8" required>
                </div>
                <div class="form-group">
                    <label>Ventas Totales (S/)</label>
                    <input type="number" step="0.01" name="ventas" value="<?php echo $ventas; ?>" required>
                </div>
                <div class="form-group">
                    <label>Antigüedad (Años)</label>
                    <input type="number" min="0" name="antiguedad" value="<?php echo $antiguedad; ?>" required>
                </div>
                <div class="form-group">
                    <label>Tienda</label>
                    <input type="text" name="tienda" value="<?php echo $tienda; ?>" required>
                </div>
                <button type="submit" class="btn-update">Calcular</button>
            </div>
        </form>
    </div>

    <?php if ($procesado): ?>
    <div class="comprobante">
        
        <div class="comp-header">
            <div class="mass-logo">Mass<span>✔</span></div>
            <h2>Reporte Mensual de Bonificaciones</h2>
            <p>Sede: <strong><?php echo $tienda; ?></strong> · Mes: <strong><?php echo date("F Y"); ?></strong></p>
        </div>

        <div class="comp-section">
            <div class="section-title">Información del Colaborador</div>
            <table class="table-data">
                <colgroup>
                    <col style="width: 40%;">
                    <col style="width: 60%;">
                </colgroup>
                <tr>
                    <td>Cajero(a):</td>
                    <td class="val-right"><?php echo $cajero; ?></td>
                </tr>
                <tr>
                    <td>Identificación DNI:</td>
                    <td class="val-right"><?php echo $dni; ?></td>
                </tr>
                <tr>
                    <td>Saludo Asignado:</td>
                    <td class="val-right">¡<?php echo $saludo; ?>!</td>
                </tr>
                <tr>
                    <td>Rendimiento Ventas:</td>
                    <td class="val-right">S/ <?php echo number_format($ventas, 2); ?></td>
                </tr>
                <tr>
                    <td>Tiempo de Servicio:</td>
                    <td class="val-right"><?php echo $antiguedad; ?> año(s)</td>
                </tr>
            </table>
        </div>

        <div class="comp-section">
            <div class="section-title">Cálculo de Incentivos y Bonos</div>
            <table class="table-data">
                <colgroup>
                    <col style="width: 65%;">
                    <col style="width: 35%;">
                </colgroup>
                <tr class="row-calc">
                    <td>Bono a las Ventas (Tasa: <?php echo ($porc_bono_ventas * 100); ?>%):</td>
                    <td class="val-right">S/ <?php echo number_format($monto_bono_ventas, 2); ?></td>
                </tr>
                <tr class="row-calc">
                    <td>Bono por Antigüedad Estructurado:</td>
                    <td class="val-right">S/ <?php echo number_format($monto_bono_antiguedad, 2); ?></td>
                </tr>
                <tr class="row-total-bonos">
                    <td>Suma Total de Incentivos:</td>
                    <td class="val-right">S/ <?php echo number_format($total_bonificaciones, 2); ?></td>
                </tr>
            </table>
        </div>

        <div class="grand-total-box">
            <span class="lbl">BONO TOTAL A PAGAR</span>
            <span class="val">S/ <?php echo number_format($total_bonificaciones, 2); ?></span>
        </div>

        <div class="saving-strip">
            🚀 Desempeño procesado el <strong><?php echo date("d/m/Y"); ?></strong> a las <strong><?php echo date("H:i:s"); ?></strong> correctamente.
        </div>

    </div>
    
    <div class="comp-footer">
        Módulo Gestión de Recursos Humanos Mass © 2026
    </div>
    <?php endif; ?>

</div>

</body>
</html>