<?php
// views/auth/barra_usuario.php
$nombre = $_SESSION['usuario']['nombre'] ?? 'Usuario';  // ✅
$rol    = $_SESSION['usuario']['rol']    ?? 'cajero';   // ✅
$tienda = $_SESSION['usuario']['tienda'] ?? 'Mass';     // ✅

// Ejercicio 2: saludo según rol
$modo = match($rol) {
    'admin'  => '🛡️ Modo administrador',
    'cajero' => '🖥️ Caja',
    default  => ucfirst($rol)
};
?>

<div style="background:#1a3a5c; color:white; padding:10px 20px;
            display:flex; justify-content:space-between; align-items:center;">
    <div>
        <strong><?= htmlspecialchars($nombre) ?></strong>
        &nbsp;|&nbsp;
        <?= htmlspecialchars($tienda) ?>
        &nbsp;|&nbsp;
        <span style="color:#9fe6b0"><?= $modo ?></span>
    </div>
    <a href="?accion=logout"
       style="background:#e74c3c; color:white; padding:6px 14px;
              border-radius:6px; text-decoration:none;">
        Salir
    </a>
</div>