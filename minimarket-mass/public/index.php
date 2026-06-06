<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../helpers/sesion.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/ProductoController.php';

$accion = $_GET['accion'] ?? 'catalogo';
$auth   = new AuthController();

switch ($accion) {

    case 'login':
        $auth->mostrarLogin();
        break;

    case 'procesar-login':
        $auth->procesarLogin();
        break;

    case 'logout':
        $auth->logout();
        break;

    case 'reset-intentos':
        $_SESSION['intentos_fallidos'] = 0;
        header('Location: index.php?accion=login');
        exit;

    // A2: panel solo para admin
    case 'panel-admin':
        requiereRol('admin');
        $usuario = usuarioActual();
        require __DIR__ . '/../views/admin/panel.php';
        break;

    case 'catalogo':
    default:
        requiereLogin();
        (new ProductoController())->listar();
        break;
}