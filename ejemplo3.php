<?php
$producto = "Aceite Primor 1L";

// 1. echo - la forma más común
echo $producto."<br>";

// 2. print - funciona igual pero devuelve 1
print $producto."<br>";

// 3. printf - como en C, con formato
printf("Producto: %s, precio: S/ %.2f", $producto, 8.90);
?>