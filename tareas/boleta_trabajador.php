<?php
// ============================================
// BOLETA DE PAGO - MINIMARKET MASS
// Trabajador: Carlos Eduardo Mamani Quispe
// Periodo: Mayo 2026
// Autor: Yenifer Juarez Hanampa
// ============================================

// 1. Datos del trabajador
$nombre      = "Carlos Eduardo Mamani Quispe";
$dni         = "74521893";
$cargo       = "Jefe de almacén";
$tienda      = "Mass Cayma";
$periodo     = "Mayo 2026";
$dias_trab   = 30;

// 2. Ingresos
$sueldo_base      = 2850.00;
$asig_familiar    = 102.50;
$horas_extras     = 12;
$valor_hora_extra = 18.50;

// 3. Tasas de descuento
$tasa_afp   = 0.13;
$tasa_renta = 0.08;

// 4. Cálculos
$pago_horas_extras   = $horas_extras * $valor_hora_extra;
$total_ingresos      = $sueldo_base + $asig_familiar + $pago_horas_extras;
$descuento_afp       = round($total_ingresos * $tasa_afp, 2);
$descuento_renta     = round($total_ingresos * $tasa_renta, 2);
$total_descuentos    = $descuento_afp + $descuento_renta;
$sueldo_neto         = $total_ingresos - $total_descuentos;

// 5. Retos adicionales
$essalud_empleador   = round($sueldo_base * 0.09, 2);
$costo_total_empresa = $total_ingresos + $essalud_empleador;
$fecha_actual        = date("d/m/Y");
$sueldo_proporcional = round($sueldo_neto / 30 * 22, 2);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de Pago - <?= $nombre ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            padding: 40px 20px;
            color: #1a2332;
        }
        .boleta {
            max-width: 750px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }

        /* CABECERA */
        .cabecera {
            background: #0066B3;
            color: white;
            padding: 28px 32px;
        }
        .cabecera h1 {
            font-size: 1.4rem;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .cabecera p {
            font-size: 0.88rem;
            opacity: 0.85;
            margin-top: 4px;
        }

        /* DATOS DEL TRABAJADOR */
        .datos {
            background: #f8fafd;
            padding: 20px 32px;
            border-bottom: 2px solid #e1e7f0;
        }
        .datos h3 {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #0066B3;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #0066B3;
        }
        .datos table { width: 100%; border-collapse: collapse; }
        .datos td {
            padding: 8px 4px;
            border-bottom: 1px dashed #e1e7f0;
            font-size: 0.92rem;
            color: #4a5670;
        }
        .datos td:last-child { text-align: right; font-weight: 600; color: #1a2332; }
        .datos tr:last-child td { border-bottom: none; }

        /* SECCIONES */
        .seccion { padding: 20px 32px; border-bottom: 1px solid #e1e7f0; }
        .seccion h3 {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid;
        }
        .h3-ingresos  { color: #2d8d4e; border-color: #2d8d4e; }
        .h3-descuentos { color: #b8392f; border-color: #b8392f; }
        .h3-adicional  { color: #0066B3; border-color: #0066B3; }

        /* TABLAS */
        .seccion table { width: 100%; border-collapse: collapse; }
        .seccion td {
            padding: 8px 4px;
            border-bottom: 1px dashed #e1e7f0;
            font-size: 0.92rem;
            color: #4a5670;
        }
        .seccion td:last-child { text-align: right; font-family: 'Courier New', monospace; color: #1a2332; }
        .seccion tr:last-child td { border-bottom: none; }

        .ingresos  { background-color: #e8f8ee; }
        .descuentos { background-color: #fde8e8; }
        .adicional  { background-color: #eef5fc; }

        .total td {
            font-weight: 700;
            font-size: 0.95rem;
            padding: 10px 8px;
            border-bottom: none !important;
        }
        .total-ingresos td   { background: #c8f0d8; color: #1f5e34; }
        .total-descuentos td { background: #f8c8c8; color: #8a2a23; }
        .total-empresa td    { background: #c8dff5; color: #004F8C; }

        /* SUELDO NETO */
        .neto {
            background: #0066B3;
            color: white;
            padding: 24px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .neto .label {
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .neto .monto {
            font-size: 1.7rem;
            font-weight: 800;
            font-family: 'Courier New', monospace;
        }

        /* PIE */
        .pie {
            background: #fff8e6;
            padding: 14px 32px;
            font-size: 0.86rem;
            color: #8a5a00;
            border-top: 3px solid #FFB81C;
        }
    </style>
</head>
<body>

<div class="boleta">

    <!-- CABECERA -->
    <div class="cabecera">
        <img src="logo.png" alt="Mass" style="height: 50px; margin-bottom: 10px; filter: brightness(0) invert(1); display: block; margin-left: auto; margin-right: auto;">
        <h1>BOLETA DE PAGO — MINIMARKET MASS</h1>
        <p>Tienda: <?= $tienda ?> &nbsp;·&nbsp; Periodo: <?= $periodo ?></p>
        <p>Fecha de emisión: <?= $fecha_actual ?></p>
    </div>

    <!-- DATOS DEL TRABAJADOR -->
    <div class="datos">
        <h3>Datos del trabajador</h3>
        <table>
            <tr><td>Trabajador</td><td><?= $nombre ?></td></tr>
            <tr><td>DNI</td><td><?= $dni ?></td></tr>
            <tr><td>Cargo</td><td><?= $cargo ?></td></tr>
            <tr><td>Días trabajados</td><td><?= $dias_trab ?> días</td></tr>
        </table>
    </div>

    <!-- INGRESOS -->
    <div class="seccion">
        <h3 class="h3-ingresos">Ingresos</h3>
        <table>
            <tr class="ingresos"><td>Sueldo base</td><td>S/ <?= number_format($sueldo_base, 2) ?></td></tr>
            <tr class="ingresos"><td>Asignación familiar</td><td>S/ <?= number_format($asig_familiar, 2) ?></td></tr>
            <tr class="ingresos"><td>Horas extras (<?= $horas_extras ?> × S/ <?= number_format($valor_hora_extra, 2) ?>)</td><td>S/ <?= number_format($pago_horas_extras, 2) ?></td></tr>
            <tr class="total total-ingresos"><td>Total ingresos</td><td>S/ <?= number_format($total_ingresos, 2) ?></td></tr>
        </table>
    </div>

    <!-- DESCUENTOS -->
    <div class="seccion">
        <h3 class="h3-descuentos">Descuentos</h3>
        <table>
            <tr class="descuentos"><td>AFP (13%)</td><td>S/ <?= number_format($descuento_afp, 2) ?></td></tr>
            <tr class="descuentos"><td>Impuesto a la Renta (8%)</td><td>S/ <?= number_format($descuento_renta, 2) ?></td></tr>
            <tr class="total total-descuentos"><td>Total descuentos</td><td>S/ <?= number_format($total_descuentos, 2) ?></td></tr>
        </table>
    </div>

    <!-- INFORMACIÓN ADICIONAL -->
    <div class="seccion">
        <h3 class="h3-adicional">Información adicional</h3>
        <table>
            <tr class="adicional"><td>EsSalud — aporte del empleador (9% del bruto)</td><td>S/ <?= number_format($essalud_empleador, 2) ?></td></tr>
            <tr class="total total-empresa"><td>Costo total para la empresa</td><td>S/ <?= number_format($costo_total_empresa, 2) ?></td></tr>
        </table>
    </div>

    <!-- SUELDO NETO -->
    <div class="neto">
        <span class="label">Sueldo neto a pagar</span>
        <span class="monto">S/ <?= number_format($sueldo_neto, 2) ?></span>
    </div>

    <!-- PIE -->
    <div class="pie">
        📋 Sueldo proporcional a 22 días trabajados: <strong>S/ <?= number_format($sueldo_proporcional, 2) ?></strong>
    </div>

</div>
</body>
</html>