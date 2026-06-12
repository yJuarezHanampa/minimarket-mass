<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../helpers/sesion.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/ProductoController.php';

$accion     = $_GET['accion'] ?? 'catalogo';
$auth       = new AuthController();
$controller = new ProductoController();

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

    // Panel solo para admin
    case 'panel-admin':
        requiereRol('admin');
        $usuario = usuarioActual();
        require __DIR__ . '/../views/admin/panel.php';
        break;

    case 'nuevo-producto':
        requiereLogin();
        $controller->nuevo();
        break;

    case 'guardar-producto':
        requiereLogin();
        $controller->guardar();
        break;

    case 'editar-producto':
        requiereLogin();
        $controller->editar();
        break;

    case 'actualizar-producto':
        requiereLogin();
        $controller->actualizar();
        break;

    case 'reportes':
        requiereLogin();
        $controller->reportes();
        break;

    case 'eliminar-producto':
    requiereLogin();
    $controller->eliminar();
    break;

    case 'catalogo':
    default:
        requiereLogin();
        $controller->listar();
        break;

}