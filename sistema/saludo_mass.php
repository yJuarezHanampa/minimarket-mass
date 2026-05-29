<?php
// ─── CONFIGURACIÓN E INICIALIZACIÓN ──────────────────
date_default_timezone_set("America/Lima");

// Capturamos la hora actual (formato de 24 horas: 00 a 23)
$hora_actual = intval(date("H"));
$minutos_actuales = date("i");

$mensaje_saludo = "";
$estado_tienda = "Abierto";
$turno = "";

// 1. Determinar el turno mediante rangos de hora
if ($hora_actual >= 5 && $hora_actual <= 11) {
    $turno = "manana";
} elseif ($hora_actual >= 12 && $hora_actual <= 18) {
    $turno = "tarde";
} elseif ($hora_actual >= 19 && $hora_actual <= 23) {
    $turno = "noche";
} else {
    $turno = "cerrado";
}

// 2. Evaluar el turno con una estructura SWITCH
switch ($turno) {
    case 'manana':
        $mensaje_saludo = "Buenos días, bienvenido a Mass"; //
        break;
    case 'tarde':
        $mensaje_saludo = "Buenas tardes, bienvenido a Mass"; //
        break;
    case 'noche':
        $mensaje_saludo = "Buenas noches, bienvenido a Mass"; //
        break;
    case 'cerrado':
    default:
        $mensaje_saludo = "Tienda cerrada en este horario"; //
        $estado_tienda = "Cerrado";
        break;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control de Turnos Mass</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #eef2f7; color: #1e293b; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        
        .container { width: 100%; max-width: 500px; }
        
        /* PANEL DE INFORMACIÓN DEL TIEMPO */
        .card-info { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 25px; border-top: 4px solid #0f3e7a; text-align: center; }
        .card-info h3 { color: #0f3e7a; font-size: 14px; text-transform: uppercase; margin-bottom: 12px; letter-spacing: 0.5px; }
        .time-display { font-size: 38px; font-weight: 800; color: #0f172a; letter-spacing: -1px; margin-bottom: 5px; }
        .date-display { font-size: 13px; color: #64748b; font-weight: 500; }

        /* TICKET DE CONTROL DE SALUDO ANTI-DESBORDE */
        .comprobante { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; width: 100%; }
        .comp-header { background: #0f3e7a; color: white; padding: 25px 20px; text-align: center; }
        .mass-logo { font-size: 42px; font-weight: 900; font-style: italic; letter-spacing: -2px; line-height: 1; margin-bottom: 10px; display: inline-block; }
        .mass-logo span { color: #ffcc00; }
        
        .comp-header h2 { font-size: 14px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 5px; }

        .comp-section { padding: 22px 25px; border-bottom: 1px solid #f1f5f9; width: 100%; }
        .section-title { color: #1e3a8a; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; padding-bottom: 5px; border-bottom: 2px solid #0f3e7a; margin-bottom: 15px; }
        
        /* CONTENEDOR CENTRAL DEL SALUDO DINÁMICO */
        .saludo-box { background: #f8fafd; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 20px 15px; text-align: center; margin-bottom: 5px; }
        .saludo-texto { font-size: 16px; font-weight: 700; color: #0f3e7a; margin-bottom: 4px; }
        .saludo-sub { font-size: 12px; color: #64748b; font-weight: 500; }

        .table-data { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .table-data td { padding: 8px 0; font-size: 13.5px; color: #334155; word-wrap: break-word; }
        .val-right { text-align: right; font-weight: 600; color: #0f172a; }
        
        /* BADGES ESTADO DE TIENDA */
        .badge { display: inline-block; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 4px; }
        .badge-open { background: #dcfce7; color: #15803d; }
        .badge-closed { background: #fef2f2; color: #b91c1c; }

        /* FRANJA DE ESTADO FINAL */
        .status-strip { padding: 15px; text-align: center; font-size: 12px; font-weight: 600; width: 100%; }
        .status-strip.open { background: #f0fdf4; color: #166534; border-top: 1px solid #bbf7d0; }
        .status-strip.closed { background: #fdf2f2; color: #991b1b; border-top: 1px solid #fca5a5; }
        
        .comp-footer { text-align: center; padding: 15px; font-size: 11px; color: #94a3b8; }
    </style>
</head>
<body>

<div class="container">

    <div class="card-info">
        <h3>Reloj del Sistema POS</h3>
        <div class="time-display"><?php echo date("H:i"); ?></div>
        <div class="date-display"><?php echo date("l, d de F Y"); ?></div>
    </div>

    <div class="comprobante">
        
        <div class="comp-header">
            <div class="mass-logo">Mass<span>✔</span></div>
            <h2>Módulo de Asignación de Saludos</h2>
        </div>

        <div class="comp-section">
            <div class="section-title">Mensaje en Pantalla Cliente</div>
            <div class="saludo-box">
                <div class="saludo-texto">"<?php echo $mensaje_saludo; ?>"</div>
                <div class="saludo-sub">Asignado mediante lógica de Turnos Intermedios</div>
            </div>
        </div>

        <div class="comp-section">
            <div class="section-title">Parámetros Evaluados</div>
            <table class="table-data">
                <colgroup>
                    <col style="width: 50%;">
                    <col style="width: 50%;">
                </colgroup>
                <tr>
                    <td>Hora del Servidor (HH):</td>
                    <td class="val-right"><?php echo $hora_actual; ?> hrs.</td>
                </tr>
                <tr>
                    <td>Turno Interno Detectado:</td>
                    <td class="val-right" style="text-transform: uppercase; font-family: monospace; font-weight: 700;">
                        <?php echo $turno; ?>
                    </td>
                </tr>
                <tr>
                    <td>Estado Operativo:</td>
                    <td class="val-right">
                        <?php if ($estado_tienda == "Abierto"): ?>
                            <span class="badge badge-open">TIENDA ABIERTA</span>
                        <?php else: ?>
                            <span class="badge badge-closed">TIENDA CERRADA</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <?php if ($estado_tienda == "Abierto"): ?>
            <div class="status-strip open">
                🏪 El POS está habilitado para procesar transacciones comerciales.
            </div>
        <?php else: ?>
            <div class="status-strip closed">
                🔒 POS Bloqueado automáticamente fuera del horario comercial (00:00 - 04:59 hrs).
            </div>
        <?php endif; ?>

    </div>
    
    <div class="comp-footer">
        Control de Accesos Automático Mass © 2026
    </div>

</div>

</body>
</html>