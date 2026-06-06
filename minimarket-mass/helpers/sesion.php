<?php
declare(strict_types=1);

function requiereLogin(): void {
    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php?accion=login');
        exit;
    }
}

function usuarioActual(): ?array {
    return $_SESSION['usuario'] ?? null;
}

// A1: guardia por rol
function requiereRol(string $rol): void {
    requiereLogin(); // primero verifica que haya sesión

    $usuario = usuarioActual();

    if ($usuario['rol'] !== $rol) {
        http_response_code(403);
        die('
            <div style="font-family:sans-serif;text-align:center;margin-top:80px">
                <h2 style="color:#dc2626">⛔ Acceso denegado</h2>
                <p>No tienes permiso para ver esta página.</p>
                <a href="index.php?accion=catalogo" 
                   style="color:#0066B3">← Volver al catálogo</a>
            </div>
        ');
    }
}