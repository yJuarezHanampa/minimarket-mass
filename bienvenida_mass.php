<?php
// ============================================
// PÁGINA DE BIENVENIDA - MINIMARKET MASS
// Autor: Yenifer Juarez Hanampa
// ============================================

// 1. Datos de la tienda
$nombre_tienda  = "Mass Cayma";
$fecha_hoy      = date("d/m/Y");

// 2. Categorías de productos
$categoria1 = "Abarrotes y despensa";
$categoria2 = "Bebidas y lácteos";
$categoria3 = "Limpieza y cuidado personal";

// 3. Promoción del día
$promocion = "¡Hoy 3x2 en todas las conservas! Solo por hoy hasta agotar stock.";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenida - <?= $nombre_tienda ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #1a2332; }
        h1 { color: #0066B3; margin-bottom: 8px; }
        h3 { color: #0066B3; margin-top: 24px; margin-bottom: 10px; }
        ul { margin-left: 20px; }
        ul li { margin-bottom: 6px; }
        .promocion { background: #fff8e6; border-left: 4px solid #FFB81C; padding: 12px 16px; margin-top: 10px; }
    </style>
</head>
<body>

    <h1>Bienvenido a Mass — <?= $nombre_tienda ?></h1>
    <p>Fecha de hoy: <?= $fecha_hoy ?></p>

    <h3>Categorías disponibles</h3>
    <ul>
        <li><?= $categoria1 ?></li>
        <li><?= $categoria2 ?></li>
        <li><?= $categoria3 ?></li>
    </ul>

    <h3>Promoción del día</h3>
    <p class="promocion"><strong><?= $promocion ?></strong></p>

</body>
</html>