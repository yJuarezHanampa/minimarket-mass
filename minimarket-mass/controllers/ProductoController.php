<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/ProductoRepository.php';

/**
 * Controlador para todo lo relacionado con productos del Mass.
 *
 * Su trabajo es:
 *   1. Recibir peticiones (a través del router).
 *   2. Pedir los datos al Model (Repository).
 *   3. Pasar esos datos a la View para que se muestren.
 *
 * NO hace lógica de negocio (eso vive en las clases del Model).
 * NO genera HTML directamente (eso vive en las Views).
 */
class ProductoController {

    private ProductoRepository $repo;

    public function __construct() {
        $this->repo = new ProductoRepository();
    }

    /**
     * Acción: mostrar la lista de todos los productos.
     * URL que la invoca: ?ruta=productos
     */
    public function listar(): void {
        // 1. Pedir datos al Model
        $productos = $this->repo->obtenerTodos();

        // 2. Pasar los datos a la View
        //    La variable $productos queda disponible dentro del archivo incluido.
        require __DIR__ . '/../views/productos/lista.php';
    }

    // Muestra el formulario
    public function nuevo(): void {
        require __DIR__ . '/../views/productos/crear.php';
    }

    // Procesa el formulario (POST)
    public function guardar(): void {
    $codigo    = trim($_POST['codigo'] ?? '');
    $nombre    = trim($_POST['nombre'] ?? '');
    $marca     = trim($_POST['marca'] ?? '');
    $categoria = (int)  ($_POST['categoria'] ?? 0);
    $precio    = (float)($_POST['precio'] ?? 0);
    $stock     = (int)  ($_POST['stock'] ?? 0);

    // Validación de campos
    if ($codigo === '' || $nombre === '' || $precio <= 0) {
        $error = 'Completa código, nombre y un precio mayor a 0.';
        require __DIR__ . '/../views/productos/crear.php';
        return;
    }

    // El código de barras es ÚNICO: si ya existe, no se repite
    if ($this->repo->buscarPorCodigo($codigo) !== null) {
        $error = 'Ya existe un producto con ese código de barras.';
        require __DIR__ . '/../views/productos/crear.php';
        return;
    }

    $this->repo->crear([
        'codigo' => $codigo, 'nombre' => $nombre, 'marca' => $marca,
        'categoria' => $categoria, 'precio' => $precio, 'stock' => $stock,
    ]);

    header('Location: index.php?accion=catalogo');  // Post-Redirect-Get
    exit;
    }

    // Muestra el formulario con los datos actuales del producto
public function editar(): void
{
    $codigo = $_GET['codigo'] ?? '';


    $producto = $this->repo->buscarPorCodigo($codigo);

    if ($producto === null) {
        header('Location: index.php?accion=catalogo');
        exit;
    }

    require __DIR__ . '/../views/productos/editar.php';
}

// Procesa el formulario y guarda los cambios (Post-Redirect-Get)
public function actualizar(): void
{
    $codigo = $_POST['codigo'] ?? '';
    $nombre = trim($_POST['nombre'] ?? '');
    $precio = $_POST['precio'] ?? '';
    $stock  = $_POST['stock']  ?? '';

    // Validación
    if ($codigo === '' || $nombre === '' || $precio === '' || $stock === '') {
        $error = 'Todos los campos son obligatorios.';
        $producto = new Producto($codigo, $nombre, (float)$precio, (int)$stock);
        require __DIR__ . '/../views/productos/editar.php';
        return;
    }

    $producto = new Producto($codigo, $nombre, (float)$precio, (int)$stock);

    $this->repo->actualizar($producto);

    header('Location: index.php?accion=catalogo&editado=1');
    exit;

}

// ── Eliminar un producto por código de barras ──────────────────────────
public function eliminar(): void {
    $codigo = trim($_GET['codigo'] ?? '');

    if ($codigo === '') {
        header('Location: index.php?accion=catalogo');
        exit;
    }

    $this->repo->eliminar($codigo);

    header('Location: index.php?accion=catalogo&eliminado=1');
    exit;
}
}
?>