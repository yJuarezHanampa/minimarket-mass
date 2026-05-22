<?php
$subtotal = "120.50";
$igv = $subtotal * 0.18;
$total = $subtotal + $igv;

echo "<h2>";
echo"BOLETA - IGV";
echo "</h2>";
echo "<br>";
echo "<b>SUBTOTAL: </b> S/ " . number_format($subtotal, 2) . "<br>";
echo "<b>IGV(18%): </b> S/ " . number_format($igv, 2)      . "<br>";
echo "<b>TOTAL:    </b> S/ " . number_format($total, 2);
?>