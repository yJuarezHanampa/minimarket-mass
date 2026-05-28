<?php // Abre el bloque de código PHP



// Captura el valor del campo "monto" enviado por el formulario

$monto = isset($_GET['monto']) ? (float)$_GET['monto'] : null; // Si existe el dato en la URL lo convierte a decimal, si no guarda null



$porcentaje = 0;    // Variable que guardará el % de descuento, empieza en 0

$mensaje_rango = "";  // Variable que guardará el texto del rango, empieza vacía



if ($monto !== null) { // Solo calcula si el usuario ya envió el formulario (monto no es null)



  if ($monto <= 30) {              // Si el monto es 30 o menos...

    $porcentaje = 0;             // ...no hay descuento

    $mensaje_rango = "Hasta S/ 30";      // ...guarda el texto del rango



  } elseif ($monto < 100) {           // Si no, y el monto es menor a 100 (rango 30.01 - 99.99)...

    $porcentaje = 5;             // ...aplica 5% de descuento

    $mensaje_rango = "De S/ 30 a S/ 99.99"; // ...guarda el texto del rango



  } elseif ($monto < 200) {           // Si no, y el monto es menor a 200 (rango 100 - 199.99)...

    $porcentaje = 10;             // ...aplica 10% de descuento

    $mensaje_rango = "De S/ 100 a S/ 199.99"; // ...guarda el texto del rango



  } else {                   // Si no entró en ningún caso anterior (monto >= 200)...

    $porcentaje = 15;             // ...aplica 15% de descuento

    $mensaje_rango = "S/ 200 o más";     // ...guarda el texto del rango

  }



  $monto_descuento = $monto * ($porcentaje / 100); // Calcula cuántos soles es el descuento (ej: 150 * 0.10 = 15)

  $monto_final  = $monto - $monto_descuento;  // Resta el descuento al monto original (ej: 150 - 15 = 135)

}

?> <!-- Cierra el bloque PHP, ahora viene HTML -->



<!DOCTYPE html> <!-- Indica que el documento es HTML5 -->

<html lang="es"> <!-- Abre el documento HTML en idioma español -->

<head>

  <meta charset="UTF-8"> <!-- Permite mostrar tildes y caracteres especiales -->

  <title>Calculadora de Descuento</title> <!-- Título que aparece en la pestaña del navegador -->

  <style> /* Estilos visuales de la página */

    body { font-family: Arial, sans-serif; max-width: 500px; margin: 40px auto; } /* Fuente, ancho máximo y centrado */

    .resultado { background: #f0f8ff; padding: 15px; border-radius: 8px; margin-top: 20px; } /* Caja azul claro para resultados */

    label { font-weight: bold; }         /* El texto del label se ve en negrita */

    input[type=number] { padding: 6px; width: 200px; } /* Tamaño del campo de texto */

    button { padding: 8px 16px; background: #4CAF50; color: white; border: none; cursor: pointer; border-radius: 4px; } /* Estilo del botón verde */

  </style>

</head>

<body> <!-- Aquí empieza el contenido visible de la página -->



<h2>Calculadora de Descuento por Monto</h2> <!-- Título principal de la página -->



<form method="GET"> <!-- Formulario que envía datos por URL al mismo archivo -->



  <label>Monto de compra (S/):</label><br><br> <!-- Texto descriptivo del campo -->



  <!-- Campo que solo acepta números, permite decimales, no negativos, y recuerda el valor enviado -->

  <input type="number" name="monto" step="0.01" min="0"

      value="<?= htmlspecialchars($_GET['monto'] ?? '') ?>">



  <button type="submit">Calcular</button> <!-- Botón que envía el formulario -->



</form>



<?php if ($monto !== null): ?> <!-- Solo muestra los resultados si el formulario fue enviado -->



<div class="resultado"> <!-- Caja contenedora de los resultados con estilo azul claro -->



  <p><strong>Rango aplicado:</strong> <?= $mensaje_rango ?></p>            <!-- Muestra el rango que le tocó al monto -->

  <p><strong>Monto original:</strong> S/ <?= number_format($monto, 2) ?></p>      <!-- Muestra el monto ingresado con 2 decimales -->

  <p><strong>Descuento aplicado:</strong> <?= $porcentaje ?>%</p>           <!-- Muestra el porcentaje de descuento -->

  <p><strong>Monto del descuento:</strong> S/ <?= number_format($monto_descuento, 2) ?></p> <!-- Muestra cuántos soles se descontaron -->

  <p><strong>Monto final a pagar:</strong> S/ <?= number_format($monto_final, 2) ?></p>  <!-- Muestra lo que el cliente debe pagar -->



</div>



<?php endif; ?> <!-- Cierra el bloque if, fin de la sección de resultados -->



</body> <!-- Cierra el contenido visible -->

</html> <!-- Cierra el documento HTML -->