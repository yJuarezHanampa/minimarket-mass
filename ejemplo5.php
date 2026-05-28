<?php
$precio_lista  = 35.90;
$descuento     = 0.15;            // 15% de descuento

$ahorro        = $precio_lista * $descuento;
$precio_final  = $precio_lista - $ahorro;

echo "Precio normal: S/ " . $precio_lista . "<br>";
echo "Ahorras: S/ " . $ahorro . "<br>";
echo "Pagas: S/ " . $precio_final;
?>