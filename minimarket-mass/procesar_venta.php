<?php
declare(strict_types=1);

// ========= IMPORTAR LAS CLASES =========
require_once 'clases/Producto.php';
require_once 'clases/Cliente.php';
require_once 'clases/Venta.php';

// ========= DATOS DEL CLIENTE =========
$cliente = new Cliente('71234567', 'María', 'Quispe');

// ========= CATÁLOGO DE PRODUCTOS MASS =========
$incaKola = new Producto('INC500', 'Inca Kola 500ml',        3.50, 48);
$gloria   = new Producto('GLO400', 'Gloria evaporada 400g',  3.80, 62);
$costeno  = new Producto('COS750', 'Arroz Costeño 750g',     4.20, 35);

// ========= PROCESAR LA VENTA =========
$venta = new Venta($cliente);
$venta->agregarProducto($incaKola, 2);
$venta->agregarProducto($gloria,   3);
$venta->agregarProducto($costeno,  1);

// ========= IMPRIMIR COMPROBANTE =========
echo "========================================\n";
echo "     MINIMARKET MASS - CAYMA\n";
echo "     Comprobante de venta\n";
echo "========================================\n";
echo "Cliente : " . $venta->getCliente()->nombreCompleto() . "\n";
echo "DNI     : " . $venta->getCliente()->getDni() . "\n";
echo "Fecha   : " . $venta->getFecha() . "\n";
echo "----------------------------------------\n";
echo "PRODUCTOS:\n";
foreach ($venta->getItems() as $item) {
    $prod = $item['producto'];
    $cant = $item['cantidad'];
    $sub  = $prod->getPrecio() * $cant;
    printf("  %-25s x%d  S/ %6.2f\n", $prod->getNombre(), $cant, $sub);
}
echo "----------------------------------------\n";
printf("Subtotal : S/ %8.2f\n", $venta->calcularSubtotal());
printf("IGV (18%%): S/ %8.2f\n", $venta->calcularIGV());
printf("TOTAL    : S/ %8.2f\n", $venta->calcularTotal());
echo "========================================\n";
?>