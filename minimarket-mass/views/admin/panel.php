<?php require __DIR__ . '/../layout/header.php'; ?>

<?php include __DIR__ . '/../auth/barra_usuario.php'; ?>

<div style="max-width:800px;margin:40px auto;padding:0 20px">

    <h1 style="color:#0066B3;font-size:28px;margin-bottom:6px">
        🛡️ Panel de administración
    </h1>
    <p style="color:#5b6677;margin-bottom:30px">
        Bienvenido, <strong><?= htmlspecialchars($usuario['nombre']) ?></strong>
    </p>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px">

        <div style="background:#fff;border:1px solid #e3e8ef;border-radius:12px;padding:20px">
            <div style="font-size:28px">📦</div>
            <div style="font-weight:700;margin-top:8px">Productos</div>
            <div style="color:#5b6677;font-size:14px">Gestionar inventario</div>
        </div>

        <div style="background:#fff;border:1px solid #e3e8ef;border-radius:12px;padding:20px">
            <div style="font-size:28px">👥</div>
            <div style="font-weight:700;margin-top:8px">Usuarios</div>
            <div style="color:#5b6677;font-size:14px">Gestionar cajeros</div>
        </div>

        <div style="background:#fff;border:1px solid #e3e8ef;border-radius:12px;padding:20px">
            <div style="font-size:28px">📊</div>
            <div style="font-weight:700;margin-top:8px">Reportes</div>
            <div style="color:#5b6677;font-size:14px">Ver ventas del día</div>
        </div>

    </div>

    <div style="margin-top:24px">
        <a href="index.php?accion=catalogo"
           style="color:#0066B3;font-size:14px">← Volver al catálogo</a>
    </div>

</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>